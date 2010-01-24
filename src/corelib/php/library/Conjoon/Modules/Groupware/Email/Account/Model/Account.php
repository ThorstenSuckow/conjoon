<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

/**
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';

/**
 * @see Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>groupware_email_accounts</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Account_Model_Account
    extends Conjoon_Db_Table implements Conjoon_BeanContext_Decoratable{

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_accounts';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Returns the id of the standard account for this user. If no standard
     * account could be found, any other account id will be returned.
     * If no account was found, 0 will be returned.
     *
     * @param int $id The id of the user to get the standard email account for
     *
     * @return integer
     */
    public function getStandardAccountIdForUser($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return 0;
        }

        $row = $this->fetchRow(
            $this->select()
                ->where('user_id=?', $userId)
                ->where('is_deleted=?', 0)
                ->where('is_standard=?', 1)
                // in case there are accidently more than 1 standard accounts
                // configured
                ->limit(1, 0)
        );

        if (!$row) {
            $row = $this->fetchRow(
                $this->select()
                    ->where('user_id=?', $userId)
                    ->where('is_deleted=?', 0)
                    ->limit(1, 0)
            );
        }

        if (!$row) {
            return 0;
        }

        return $row->id;
    }

    /**
     * Removes any account which is flagged as "is_deleted=1" along with its
     * informations, if there are no items for this account in the related folders
     * anymore.
     *
     * @param integer $userId
     *
     * @return array An array with the deleted account ids
     */
    public function removeAsDeletedFlaggedAccounts($userId)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return array();
        }

        $rows = $this->fetchAll(
            $this->select()
                ->where('user_id=?', $id)
                ->where('is_deleted=1')
        );

        $deletedIds = array();

        foreach ($rows as $row) {
            $res = $this->_removeInformationForAccountIf($row->id, $userId);
            if ($res) {
                $deletedIds[] = $res;
            }
        }

        return $deletedIds;
    }

    private function _removeInformationForAccountIf($accountId, $userId)
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersAccounts.php';

        $foldersAccounts = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts();

        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Model_Item
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Item.php';

        $itemModel = new Conjoon_Modules_Groupware_Email_Item_Model_Item();

        $folders = $foldersAccounts->getFolderIdsForAccountId($accountId);
        $del = true;
        for ($i = 0, $len = count($folders); $i < $len; $i++) {
            $count = $itemModel->getEmailItemCountForFolder($folders[$i]);
            if ($count > 0) {
                $del = false;
                break;
            }
        }

        if ($del) {
            // remove account-data entirely - as for accounts_root data specified
            $where   = $this->getAdapter()->quoteInto('id = ?', $accountId, 'INTEGER');
            $deleted = $this->delete($where);

            if ($deleted) {
                // delete account-mappings
                $foldersAccounts->deleteForAccountId($accountId);

                return $accountId;
            }
        }

        return 0;
    }

    /**
     * Sets the is_deleted flag of the account to "0", which basically means that
     * this account is not active anymore. However, in order for older email items
     * to still work properly, folder mappings have to remain, until no
     * item for this account is found in the database. After that, the account may
     * be removed entirely.
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return integer The number of accounts deleted.
     */
    public function deleteAccount($accountId, $userId)
    {
        $accountId = (int)$accountId;
        $userId    = (int)$userId;

        if ($accountId <= 0 || $userId <= 0) {
            return 0;
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_Folder
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';

        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();

        $rootId = $folderModel->getRootFolderId($accountId, $userId);

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersAccounts.php';

        $foldersAccounts = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts();

        $folders = $foldersAccounts->getFolderIdsForAccountId($accountId);

        // rootID found - delete all!
        if ($rootId) {
            // no folders or root - we can remove the account entirely
            $where   = $this->getAdapter()->quoteInto('id = ?', $accountId, 'INTEGER');
            $deleted = $this->delete($where);
            if ($deleted) {
                for ($i = 0, $len = count($folders); $i < $len; $i++) {
                    $folderModel->deleteFolder($folders[$i], $userId, false);
                }
            }

            return $deleted;
        } else {
            // no root id. Check if there are any items still in folders
            // belonging to the account. If that is the case, DO NOT remove the
            // account from the data storage
            $res = $this->_removeInformationForAccountIf($accountId, $userId);

            if ($res) {
                return 1;
            } else {
                // update account to is_deleted = 1
                $where = $this->getAdapter()->quoteInto('id = ?', $accountId, 'INTEGER');
                return $this->update(array('is_deleted' => 1), $where);
            }
        }

    }

    /**
     * Adds an account for the specified user with the specified data.
     *
     * @param integer $userId The userid this account will belong to
     * @param array $data an assoc array with keys as fields and values as data
     * to add
     *
     * Will also take the default folder of the user and copy them so emails
     * for this account will also be stored in this folders.
     *
     * @return integer 0 if the data wasn't added, otherwise the id of the newly added
     * row
     */
    public function addAccount($userId, Array $data)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return 0;
        }

        $whiteList = array(
            'name',
            'address',
            'protocol',
            'server_inbox',
            'server_outbox',
            'username_inbox',
            'username_outbox',
            'user_name',
            'is_outbox_auth',
            'password_inbox',
            'password_outbox',
            'port_inbox',
            'port_outbox',
            'inbox_connection_type',
            'outbox_connection_type'
        );

        $addData = array();

        foreach ($data as $key => $value) {
            if (in_array($key, $whiteList)) {
                $addData[$key] = $value;
            }
        }

        if (empty($addData)) {
            return 0;
        }

        // check here if there is currently a standard account
        // configured for the user - if none could be found, set this
        // account as standard
        $standardId = $this->getStandardAccountIdForUser($userId);

        if ($standardId == 0) {
            $addData['is_standard'] = 1;
        }

        $addData['user_id'] = $userId;

        $id = $this->insert($addData);

        if ((int)$id == 0) {
            return $id;
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_Folder
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';

        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();

        if ($addData['protocol'] == 'IMAP') {
            $folderModel->createFolderHierarchyForImapAccount($id, $userId, $addData['name']);
        } else {
            $folderModel->createFolderBaseHierarchyAndMapAccountIdForUserId($id, $userId);
        }


        return $id;
    }

    /**
     * Updates an account with the specified data.
     *
     * @param integer $id The id of the account to update
     * @param array $data an assoc array with keys as fields and values as data
     * to update
     *
     * @return integer The number of rows updated. If any input error happened, -1
     * will be returned. 0 will be returned, if the data was not changed, i.e. if
     * the row equaled to teh data tried to update. 1 will be returned if any changes to
     * the data has been made.
     */
    public function updateAccount($id, Array $data)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return -1;
        }

        $updateData = array();

        $whiteList = array(
            'name',
            'address',
            'reply_address',
            'is_standard',
            'protocol',
            'server_inbox',
            'server_outbox',
            'username_inbox',
            'username_outbox',
            'user_name',
            'is_outbox_auth',
            'password_inbox',
            'password_outbox',
            'signature',
            'is_signature_used',
            'port_inbox',
            'port_outbox',
            'is_copy_left_on_server',
            'inbox_connection_type',
            'outbox_connection_type'
        );

        foreach ($data as $key => $value) {
            if (in_array($key, $whiteList)) {
                $updateData[$key] = $value;
            }
        }

        if (empty($updateData)) {
            return -1;
        }

        $where = $this->getAdapter()->quoteInto('id = ?', $id, 'INTEGER');
        return $this->update($updateData, $where);
    }

    /**
     * Returns the account with the specified id for the specified user
     *
     * @param int $id The accountId the id of the account to query
     * @param int $userId The id of the user to whom the specified account belongs
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getAccount($accountId, $userId)
    {
        $accountId = (int)$accountId;
        $userId    = (int)$userId;

        if ($accountId <= 0 || $userId <= 0) {
            return null;
        }

        $row = $this->fetchRow(
            $this->select()
                ->where('id=?', $accountId)
                ->where('user_id=?', $userId)
                ->where('is_deleted=?', false)
        );

        return $row;
    }

    /**
     * Returns all email addresses which are configured for a user.
     * This takes also reply-addresses into account.
     *
     * @param int $id The id of the user to get the email addresses for
     *
     * @return array A numeric array with all email addresses found in the accounts
     * from the user
     */
    public function getEmailAddressesForUser($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return array();
        }

        $rows = $this->fetchAll(
            $this->select($this, array('address', 'reply_address'))
                ->where('user_id=?', $id)
                ->where('is_deleted=?', false)
                ->order('is_standard DESC')
        );

        $data = array();

        foreach ($rows as $row) {
            if ($row->address != "") {
                $data[] = $row->address;
            }

            if ($row->reply_address != "") {
                $data[] = $row->reply_address;
            }
        }

        return array_unique($data);;
    }

    /**
     * Returns all email accounts for the specified user-id.
     * Note, that the field 'user_id' won't be available in the returned
     * array.
     *
     * @param int $id The id of the user to get the email accounts for
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getAccountsForUser($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return array();
        }

        $rows = $this->fetchAll(
            $this->select()
                ->where('user_id=?', $id)
                ->where('is_deleted=?', false)
                ->order('is_standard DESC')
        );

        return $rows;
    }

// -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Groupware_Email_Account';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getAccountsForUser',
            'getAccount'
        );
    }

}