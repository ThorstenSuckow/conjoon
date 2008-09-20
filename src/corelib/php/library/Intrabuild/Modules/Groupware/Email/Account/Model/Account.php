<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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
 * @see Zend_Db_Table
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @see Intrabuild_BeanContext_Decoratable
 */
require_once 'Intrabuild/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>groupware_email_accounts</tt>.
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Account_Model_Account
    extends Zend_Db_Table_Abstract implements Intrabuild_BeanContext_Decoratable{

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
     * Updates the is_deleted field in the table to "1" to indicate that this account
     * was deleted and should not be used anymore.
     *
     * @return integer The number of accounts deleted.
     */
    public function deleteAccount($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return 0;
        }

        $where    = $this->getAdapter()->quoteInto('id = ?', $id, 'INTEGER');
        $affected = $this->update(array(
            'is_standard' => 0,
            'is_deleted'  => 1
        ), $where);

        return $affected;
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
            'server_inbox',
            'server_outbox',
            'username_inbox',
            'username_outbox',
            'user_name',
            'is_outbox_auth',
            'password_inbox',
            'password_outbox'
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

        $addData['user_id'] = $userId;

        $id = $this->insert($addData);

        if ((int)$id == 0) {
            return $id;
        }

        /**
         * @see Intrabuild_Modules_Groupware_Email_Folder_Model_Folder
         */
        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Model/Folder.php';

        $folderModel = new Intrabuild_Modules_Groupware_Email_Folder_Model_Folder();
        $folderIds   = $folderModel->getFoldersForAccountsRoot($userId);

        if (empty($folderIds)) {
            // user creates his very first email account.
            // create base folder hierarchy and map them to the
            // account later on
            $folderIds = $folderModel->addAccountsRootBaseHierarchy();
        }


        /**
         * @see Intrabuild_Modules_Groupware_Email_Folder_Model_FoldersAccounts
         */
        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Model/FoldersAccounts.php';
        $foldersAccountsModel = new Intrabuild_Modules_Groupware_Email_Folder_Model_FoldersAccounts();

        // map all existing folders from the accounts_root hierarchy to the new account
        for ($i = 0, $len = count($folderIds); $i < $len; $i++) {
            $foldersAccountsModel->insert(array(
                'groupware_email_folders_id'  => $folderIds[$i],
                'groupware_email_accounts_id' => $id
            ));
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
            'is_copy_left_on_server'
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

// -------- interface Intrabuild_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Intrabuild_Modules_Groupware_Email_Account';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getAccountsForUser',
            'getAccount'
        );
    }

}