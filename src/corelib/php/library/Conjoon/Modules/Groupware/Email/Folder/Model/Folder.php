<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * Table data gateway. Models the table <tt>groupware_email_folders</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Folder_Model_Folder
    extends Conjoon_Db_Table implements Conjoon_BeanContext_Decoratable{

    const META_INFO_OUTBOX = 'outbox';
    const META_INFO_DRAFT  = 'draft';
    const META_INFO_SENT   = 'sent';
    const META_INFO_INBOX  = 'inbox';


    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_folders';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Returns the type of the root node for the specified folder id.
     * Returns null if the folder was not found.
     *
     * @param integer $folderId
     *
     * @return String
     *
     * @throws Conjoon_Argument_Exception
     */
    public function getRootTypeForFolderId($folderId)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        $data = array('folderId' => $folderId);

        Conjoon_Argument_Check::check(array(
            'folderId' => array(
                'allowEmpty' => false,
                'type'       => 'int'
            )
        ), $data);

        $folderId = $data['folderId'];

        $where = $this->getAdapter()->quoteInto('id = ?', $folderId, 'INTEGER');

        $select = $this->select()
            ->from($this, array('parent_id', 'type'))
            ->where($where);

        $row = $this->fetchRow($select);

        if ($row && $row->parent_id == null) {
            return $row->type;
        }

        if (!$row) {
            return null;
        }

        return $this->getRootTypeForFolderId($row->parent_id);
    }

    /**
     * Returns the path beginning from the root folder to the specified folder,
     * inluding the specified folder, as an array.
     *
     * @param integer $folderId
     *
     * @return array
     *
     * @throws Conjoon_Argument_Exception
     */
    public function getPathForFolderId($folderId)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        $data = array('folderId' => $folderId);

        Conjoon_Argument_Check::check(array(
            'folderId' => array(
                'type'       => 'int',
                'allowEmpty' => false
            )), $data);

        $folderId = $data['folderId'];

        $adapter = $this->getAdapter();

        $where = $adapter->quoteInto('id = ?', $folderId, 'INTEGER');

        $select = $this->select()
            ->from($this, array('id', 'parent_id'))
            ->where($where);

        $row = $adapter->fetchRow($select);

        if ($row['parent_id'] == null) {
            return array($folderId);
        }

        return array_merge(
            $this->getPathForFolderId($row['parent_id']),
            array($folderId)
        );
    }

    /**
     * Returns all ids of all child folders as a flat array without
     * hierarchy.
     *
     * @param integer $folderId
     *
     * @return array
     *
     * @throws Conjoon_Argument_Exception
     */
    public function getChildFolderIdsAsFlatArray($folderId)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        $data = array('folderId' => $folderId);

        Conjoon_Argument_Check::check(array(
            'folderId' => array(
                'type'       => 'int',
                'allowEmpty' => false
        )), $data);

        $folderId = $data['folderId'];

        $adapter = $this->getAdapter();

        $where = $adapter->quoteInto('parent_id = ?', $folderId, 'INTEGER');

        $select = $this->select()
            ->from($this, array('id'))
            ->where($where);

        $rows = $adapter->fetchAll($select);

        $ids = array();

        foreach ($rows as $row) {
            $ids[] = $row['id'];
            $tmpIds = $this->getChildFolderIdsAsFlatArray($row['id']);

            if (!empty($tmpIds)) {
                $ids = array_merge($ids, $tmpIds);
            }
        }

        return $ids;
    }


    /**
     * Returns the base sent folder id for the specified account and the specified
     * user, i.e. the folder with type "sent".
     * Returns 0 if the folder could not be found
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return integer
     *
     * @deprecated use getSentFolderId() instead
     */
    public function getSentFolder($accountId, $userId)
    {
        return $this->_getDefaultFolderIdForType($accountId, $userId, 'sent');
    }

    /**
     * Returns the base data for the root folder mapped to a specific account
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return Zend_Db_Table_Row or null
     */
    public function getAnyRootMailFolderBaseData($accountId, $userId) {

        $accountId = (int)$accountId;
        $userId    = (int)$userId;

        if ($accountId <= 0 || $userId <= 0) {
            return null;
        }

        $folderId = $this->getAccountsRootOrRootFolderId($accountId, $userId);

        return $this->getFolderBaseData($folderId);
    }

    /**
     * Returns the base root/ folder id for the specified account
     * and the specified user, i.e. the folder with type "root".
     * Returns 0 if the folder could not be found
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return integer
     *
     */
    public function getRootFolderId($accountId, $userId)
    {
        return $this->_getDefaultFolderIdForType($accountId, $userId, 'root');
    }

    /**
     * Returns the base accounts_root folder id for the specified account
     * and the specified user, i.e. the folder with type "accounts_root".
     * Returns 0 if the folder could not be found
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return integer
     *
     */
    public function getAccountsRootFolderId($accountId, $userId)
    {
        return $this->_getDefaultFolderIdForType($accountId, $userId, 'accounts_root');
    }

    /**
     * Returns the base root_remote folder id for the specified account
     * and the specified user, i.e. the folder with type "root_remote".
     * Returns 0 if the folder could not be found
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return integer
     *
     */
    public function getRootRemoteFolderId($accountId, $userId)
    {
        return $this->_getDefaultFolderIdForType($accountId, $userId, 'root_remote');
    }

    /**
     * Returns the root/accounts_root folder id for the specified account
     * and the specified user, i.e. the folder with type "root" or "accounts_root".
     * Returns 0 if the folder could not be found
     * If no accounts_root could be found, the method will look up a "root" marked folder.
     * If no "root" folder was found, the "root_remote" folder will be queried.
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return integer
     *
     */
    public function getAccountsRootOrRootFolderId($accountId, $userId)
    {
        $root = $this->_getDefaultFolderIdForType($accountId, $userId, 'accounts_root');

        if ($root == 0) {
            $root = $this->_getDefaultFolderIdForType($accountId, $userId, 'root');
        }

        if ($root == 0) {
            $root = $this->_getDefaultFolderIdForType($accountId, $userId, 'root_remote');
        }

        return $root;
    }

    /**
     * Returns the base sent folder id for the specified account and the specified
     * user, i.e. the folder with type "sent".
     * Returns 0 if the folder could not be found
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return integer
     */
    public function getSentFolderId($accountId, $userId)
    {
        return $this->_getDefaultFolderIdForType($accountId, $userId, 'sent');
    }

    /**
     * Returns the base draft folder id for the specified account and the specified
     * user, i.e. the folder with type "draft".
     * Returns 0 if the folder could not be found
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return integer
     */
    public function getDraftFolderId($accountId, $userId)
    {
        return $this->_getDefaultFolderIdForType($accountId, $userId, self::META_INFO_DRAFT);
    }

    /**
     * Returns the base inbox folder id for the specified account and the specified
     * user, i.e. the folder with type "inbox".
     * Returns 0 if the folder could not be found
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return integer
     */
    public function getInboxFolderId($accountId, $userId)
    {
        return $this->_getDefaultFolderIdForType(
            $accountId, $userId, self::META_INFO_INBOX
        );
    }

    /**
     * Returns the base outbox folder id for the specified account and the specified
     * user, i.e. the folder with type "outbox".
     * Returns 0 if the folder could not be found
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return integer
     */
    public function getOutboxFolderId($accountId, $userId)
    {
        return $this->_getDefaultFolderIdForType($accountId, $userId, self::META_INFO_OUTBOX);
    }

    /**
     * Returns the meta info for the folder with the specified id.
     * Returns null if the folder was not found.
     *
     * @param integer $folderId
     *
     * @return string
     */
    public function getMetaInfo($folderId)
    {
        $folderId = (int)$folderId;

        if ($folderId <= 0) {
            return null;
        }

        $where  = $this->getAdapter()->quoteInto('id = ?', $folderId, 'INTEGER');

        // check first if the folder may get deleted
        $select = $this->select()
                  ->from($this, array('meta_info'))
                  ->where($where);

        $row = $this->fetchRow($select);

        if ($row) {
            return $row->meta_info;
        }

        return null;
    }

    /**
     * Returns true if the folder's name may be edited, otherwise false.
     *
     * @param integer $id The id of the folder to rename
     *
     * @return boolean true if the folder may be renamed, otherwise false
     */
    public function isFolderNameEditable($id)
    {
        $where  = $this->getAdapter()->quoteInto('id = ?', $id, 'INTEGER');

        // check first if the folder may get deleted
        $select = $this->select()
                  ->from($this, array('is_locked'))
                  ->where($where);

        $row = $this->fetchRow($select);

        if ($row) {
            if ($row->is_locked) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Returns the id of the folder with the the sppecified name for the specified
     * parent folder. Returns 0 if no wolder was found.
     *
     * @param string $name
     * @param integer $parentId
     *
     * @return integer
     */
    public function getFolderIdForName($name, $parentId)
    {
        $parentId = (int)$parentId;

        if ($parentId <= 0) {
            return 0;
        }

        $where1 = $this->getAdapter()->quoteInto('parent_id = ?', $parentId, 'INTEGER');
        $where2 = $this->getAdapter()->quoteInto('name = ?', $name);

        // check first if the folder may get deleted
        $select = $this->select()
                  ->from($this, array('id'))
                  ->where($where1)
                  ->where($where2);

        $row = $this->fetchRow($select);

        if ($row) {
            return $row->id;
        }

        return 0;
    }

    /**
     * Returns true if the folder may be deleted, otherwise false.
     *
     * @param integer $id The id of the folder to delete
     * @param integer $userId If specified, the owner status will also be
     * considered
     *
     * @return boolean true if the folder may be deleted, otherwise false
     */
    public function isFolderDeletable($id, $userId = -1)
    {
        $where  = $this->getAdapter()->quoteInto('id = ?', $id, 'INTEGER');

        // check first if the folder may get deleted
        $select = $this->select()
                  ->from($this, array('is_locked'))
                  ->where($where);

        $row = $this->fetchRow($select);

        if ($row) {
            if ($row->is_locked) {
                return false;
            }
        } else {
            return false;
        }

        if ($userId != -1) {

            /**
             * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers
             */
            require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersUsers.php';

            $foldersUsers = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers();
            $ret = $foldersUsers->getRelationShipForFolderAndUser(
                $id, $userId
            );

            if ($ret === Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers::OWNER) {
                return true;
            }
        }

        return true;
    }

    /**
     * Returns true if the specified folder allows child nodes to be added,
     * otherwise false.
     *
     * @param integer $id The id of the folder to check
     *
     * @return boolean true if the folder allows child nodes to be appended,
     * otherwise false
     */
    public function doesFolderAllowChildren($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return false;
        }

        $where  = $this->getAdapter()->quoteInto('id = ?', $id, 'INTEGER');

        $select = $this->select()
                  ->from($this, array('is_child_allowed'))
                  ->where($where);

        $row = $this->fetchRow($select);

        if ($row && $row->is_child_allowed) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the specified folder may be moved to the , otherwise false.
     *
     * @param integer $id The id of the folder to delete
     * @param integer $parentId The id of the folder the folder gets moved into
     *
     * @return boolean true if the folder may be deleted, otherwise false
     */
    public function isFolderMoveableToFolder($id, $parentId)
    {
        $where  = $this->getAdapter()->quoteInto('id = ?', $id, 'INTEGER');
        // check first if the folder may get deleted
        $select = $this->select()
                  ->from($this, array('is_locked'))
                  ->where($where);

        $row = $this->fetchRow($select);

        if ($row) {
            if ($row->is_locked) {
                return false;
            }
        } else {
            return false;
        }

        return $this->doesFolderAllowChildren($parentId);
    }


    /**
     * Returns the total count of messages for the specified folder
     * belonging to the specified user.
     *
     * @param integer $folderId
     * @param integer $userId
     *
     * @return integer the total count of items, or 0 if an error occured
     * or no items where available.
     */
    public function getItemCountForFolderAndUser($folderId, $userId)
    {
        $folderId = (int)$folderId;
        $userId   = (int)$userId;

        if ($folderId <= 0 || $userId <= 0) {
            return 0;
        }

        $adapter = $this->getAdapter();

        $select = $adapter->select()
                  ->from(self::getTablePrefix() . 'groupware_email_items', array(
                    'COUNT(id) as count_id'
                  ))
                  ->join(
                        array('flags' => self::getTablePrefix() . 'groupware_email_items_flags'),
                        'flags.groupware_email_items_id=' . self::getTablePrefix(). 'groupware_email_items.id '.
                        ' AND ' .
                        'flags.is_deleted=0 '.
                        'AND '.
                        $adapter->quoteInto('flags.user_id=?', $userId, 'INTEGER'),
                        array())
                 ->where('groupware_email_folders_id = ?', $folderId);

        $row = $adapter->fetchRow($select);

        return ($row != null) ? $row['count_id'] : 0;
    }

    /**
     * Returns the total count of pendning messages for the specified folder
     * belonging to the specified user.
     *
     * A pending item is either a message flagged as unread, or a message
     * in a draft or outbox folder waiting to be edited/send.
     *
     * @param integer $folderId
     * @param integer $userId
     *
     * @return integer the total count of pending items, or 0 if an error occured
     * or no items where available.
     */
    public function getPendingCountForFolderAndUser($folderId, $userId)
    {
        $folderId = (int)$folderId;
        $userId   = (int)$userId;

        if ($folderId <= 0 || $userId <= 0) {
            return 0;
        }

        $adapter = $this->getAdapter();

        $select = $adapter->select()
                  ->from(array('folders' => self::getTablePrefix() . 'groupware_email_folders'), array(
                    'meta_info'
                  ))
                  ->joinLeft(
                    array('items' => self::getTablePrefix() . 'groupware_email_items'),
                    'items.groupware_email_folders_id=folders.id'
                  )
                  ->joinLeft(
                        array('flag' => self::getTablePrefix() . 'groupware_email_items_flags'),
                        'flag.groupware_email_items_id=items.id '.
                        ' AND ' .
                        'flag.is_read=0 '.
                        ' AND ' .
                        'flag.is_deleted=0 '.
                        'AND '.
                        $adapter->quoteInto('flag.user_id=?', $userId, 'INTEGER'),
                        array('pending_count' => "IF (folders.meta_info !='draft' AND folders.meta_info !='outbox' ,COUNT(DISTINCT flag.groupware_email_items_id), COUNT(DISTINCT items.id))")
                 )
                 ->where('folders.id = ?', $folderId)
                 ->where('folders.is_deleted = ?', 0)
                 ->group('folders.id');

        $row = $adapter->fetchRow($select);

        return ($row != null) ? $row['pending_count'] : 0;
    }

    /**
     * Adds a default folder hierarchy and returns all the id's added
     * in a flat numeric array.
     *
     * This method will also store the relation of the user and the created folders
     * in the folders_users table.
     *
     * The folder hierarchy created will be used to store messages for
     * a single local account, thus the type forwarded is root.
     *
     * @param $userId
     * @param $name The name which should be used for the root folder
     *
     * @see createLocalRootHierarchy
     * @see addAccountsRootBaseHierarchy
     */
    protected function addLocalRootBaseHierarchy($userId, $name) {
        return $this->createLocalRootHierarchy(
            $userId, 'root', $name
        );
    }

    /**
     * Adds a default folder hierarchy and returns all the id's added
     * in a flat numeric array.
     *
     * This method will also store the relation of the user and the created folders
     * in the folders_users table.
     *
     * The folder hierarchy created will be used to store messages from
     * multiple accounts, thus the type forwarded is accounts_root.
     *
     * @param integer $userId
     *
     * @return array
     *
     * @see createLocalRootHierarchy
     * @see addLocalRootBaseHierarchy
     */
    protected function addAccountsRootBaseHierarchy($userId) {
        return $this->createLocalRootHierarchy(
            $userId, 'accounts_root', 'Local Folders'
        );
    }

    /**
     * Adds a default folder hierarchy and returns all the id's added
     * in a flat numeric array.
     *
     * This method will also store the relation of the user and the created folders
     * in the folders_users table.
     *
     * @param integer $userId
     * @param string $rootTpye The type of root folder - either root or
     *                         accounts_root
     * @param string $name The name which should be used for the root folder
     *
     * @return array
     */
    protected function createLocalRootHierarchy($userId, $rootType, $name)
    {
        $rootTypeValues = array('accounts_root', 'root');

        $rootType = strtolower(trim((string)$rootType));
        $userId   = (int)$userId;
        $name     = trim((string)$name);

        if ($userId == 0 || $name == "" ||
            !in_array($rootType, $rootTypeValues)) {
            return array();
        }

        $adapter = $this->getAdapter();

        $adapter->beginTransaction();

        $ids = array();

        try {

            // root folder
            $parentId = $this->insert(array(
                'name'             => $name,
                'is_child_allowed' => 0,
                'is_locked'        => 1,
                'type'             => $rootType,
                'meta_info'        => 'inbox',
                'parent_id'        => null
            ));
            $ids[] = $parentId;

            // inbox folder
            $id = $this->insert(array(
                'name'             => 'Inbox',
                'is_child_allowed' => 1,
                'is_locked'        => 1,
                'type'             => 'inbox',
                'meta_info'        => 'inbox',
                'parent_id'        => $parentId
            ));
            $ids[] = $id;

            // spam folder
            $id = $this->insert(array(
                'name'             => 'Spam',
                'is_child_allowed' => 1,
                'is_locked'        => 1,
                'type'             => 'spam',
                'meta_info'        => 'inbox',
                'parent_id'        => $parentId
            ));
            $ids[] = $id;

            // outbox folder
            $id = $this->insert(array(
                'name'             => 'Outbox',
                'is_child_allowed' => 0,
                'is_locked'        => 1,
                'type'             => 'outbox',
                'meta_info'        => 'outbox',
                'parent_id'        => $parentId
            ));
            $ids[] = $id;

            // draft folder
            $id = $this->insert(array(
                'name'             => 'Drafts',
                'is_child_allowed' => 0,
                'is_locked'        => 1,
                'type'             => 'draft',
                'meta_info'        => 'draft',
                'parent_id'        => $parentId
            ));
            $ids[] = $id;

            // sent folder
            $id = $this->insert(array(
                'name'             => 'Sent',
                'is_child_allowed' => 0,
                'is_locked'        => 1,
                'type'             => 'sent',
                'meta_info'        => 'sent',
                'parent_id'        => $parentId
            ));
            $ids[] = $id;

            // trash folder
            $id = $this->insert(array(
                'name'             => 'Trash',
                'is_child_allowed' => 1,
                'is_locked'        => 1,
                'type'             => 'trash',
                'meta_info'        => 'inbox',
                'parent_id'        => $parentId
            ));
            $ids[] = $id;

            /**
             * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers
             */
            require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersUsers.php';

            $foldersUsers = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers();
            $foldersUsers->addRelationship(
                $ids, $userId, Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers::OWNER
            );

            $adapter->commit();

            return $ids;

        } catch (Exception $e) {
            $adapter->rollBack();
            return array();
        }

    }



    /**
     * Deletes a folder and all related data permanently from the datastore.
     *
     * @todo  The method will first try to delete the folder related data primary,
     * and signal a successfull operation even if other contextual data (such as
     * email items stored in this folder) could not be deleted. In this case,
     * a script should from time to time determine which email items etc. are
     * not related to a  folder anymore.
     *
     * @param integer $id The id of the folder to delete
     * @param integer $userId The id of the user to delete the data for
     * @param boolean $checkForDeletable Check whether the folder may get deleted
     *
     * @return integer 0 if the folder was not deleted, otherwise 1 (equals to
     * the number of deleted folders)
     */
    public function deleteFolder($id, $userId, $checkForDeletable = true)
    {
        $id     = (int)$id;
        $userId = (int)$userId;
        if ($id <= 0 || $userId <= 0) {
            return 0;
        }

        // check first if the folder may get deleted
        if ($checkForDeletable && !$this->isFolderDeletable($id, $userId)) {
            return 0;
        }

        $where  = $this->getAdapter()->quoteInto('id = ?', $id, 'INTEGER');
        $affected = $this->delete($where);

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersUsers.php';
        $faModel = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers();
        $faModel->deleteForFolder($id);

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersAccounts.php';
        $faModel = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts();
        $faModel->deleteForFolder($id);

        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Model_Item
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Item.php';
        $itemModel = new Conjoon_Modules_Groupware_Email_Item_Model_Item();
        $itemModel->deleteItemsForFolder($id, $userId);


        return $affected;

    }

    /**
     * Moves a folder to a new node.
     *
     * @param integer $id The id of the folder to move
     * @param integer $newParentId The id of the folder to which the folder specified
     * with $id gets moved to
     *
     * @return integer The number of rows updated, i.e. 0 if no update happened,
     * otherwise 1
     */
    public function moveFolder($id, $parentId)
    {
        $id       = (int)$id;
        $parentId = (int)$parentId;

        if ($id <= 0 || $parentId <= 0) {
            return 0;
        }

        // check first if the folder may be moved.
        if (!$this->isFolderMoveableToFolder($id, $parentId)) {
            return 0;
        }

        // check now if a folder with the same name already exists
        $folder       = $this->getFolderBaseData($id);
        $nameFolderId = $this->getFolderIdForName($folder->name, $parentId);
        if ($nameFolderId != 0 && $nameFolderId != $id) {
            return 0;
        }

        $data = array('parent_id' => $parentId);
        $adapter = $this->getAdapter();
        return $this->update($data, array(
            $adapter->quoteInto('id = ?', $id, 'INTEGER')
        ));
    }

    /**
     * Renames a folder.
     *
     * @param integer $id The id of the folder to rename
     * @param string $name The new name of the folder
     *
     * @return integer The number of rows updated, i.e. 0 if no update happened,
     * otherwise 1
     */
    public function renameFolder($id, $name)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return 0;
        }

        if (!$this->isFolderNameEditable($id)) {
            return 0;
        }

        // check now if a folder with the same name already exists
        $folder       = $this->getFolderBaseData($id);
        $nameFolderId = $this->getFolderIdForName($name, $folder->parent_id);
        if ($nameFolderId != 0 && $nameFolderId != $id) {
            return 0;
        }

        $adapter = $this->getAdapter();
        $data = array('name' => $name);
        return $this->update($data, array(
            $adapter->quoteInto('id = ?', $id, 'INTEGER')
        ));
    }

    /**
     * Appends a new folder to the folder with the specified $parentId.
     * If the folder is connected to an email-account, the new folder
     * will inherit the account-id from it's parent-folder. The new folder
     * will also inherit the meta-info-value of the parent folder
     *
     * Checks first if the
     *
     * @param integer $parentId
     * @param string $name
     * @param integer $userId
     */
    public function addFolder($parentId, $name, $userId)
    {
        if ((int)$parentId <= 0 || (int)$userId <= 0 || $name == "") {
            return -1;
        }

        if (!$this->doesFolderAllowChildren($parentId)) {
            return -1;
        }

        // check now if a folder with the same name already exists
        if ($this->getFolderIdForName($name, $parentId) != 0) {
            return -1;
        }

        $parentRow = $this->fetchRow($parentId)->toArray();

        if (!is_array($parentRow) || $parentRow['meta_info'] == null) {
            return -1;
        }

        $data = array(
            'name'             => $name,
            'is_child_allowed' => 1,
            'is_locked'        => 0,
            'type'             => 'folder',
            'parent_id'        => $parentId,
            'meta_info'        => $parentRow['meta_info']
        );

        $id = (int)$this->insert($data);

        if ($id <= 0) {
            return -1;
        }

        // check if the parent folder is related to one ore more accounts
        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersAccounts.php';
        $foldersAccountsModel = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts();

        $foldersAccountsModel->inheritFromParentIdForFolderId($parentId, $id);

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersUsers.php';
        $foldersUsersModel = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers();

        $foldersUsersModel->inheritFromParentIdForFolderId($parentId, $id);

        return $id;
   }

    /**
     * Returns the base data for the accounts_root folder for the specified user.
     * Returns null if no accounts_root folder is available for this user yet
     *
     * @param integer $userId
     *
     * @return Zend_db_Table_Row or null if not available
     */
    public function getAccountsRootMailFolderBaseData($userId) {

        $userId = (int)$userId;

        if ($userId <= 0) {
            return null;
        }

        $where = $this->getAdapter()->quoteInto('folders.type=?', 'accounts_root');

        // check first if the folder may get deleted
        $select = $this->select()
                  ->from(array('folders' => self::getTablePrefix() . 'groupware_email_folders'),
                         array('*', 'id AS id_for_path', '(1) as is_selectable'))
                  ->join(
                      array('folders_users' => self::getTablePrefix() . 'groupware_email_folders_users'),
                      'folders_users.groupware_email_folders_id=folders.id' .
                      ' AND ' .
                      'folders_users.users_id=' . $userId .
                      ' AND ' .
                      'folders_users.relationship=\'owner\'',
                      array()
                  )
                  //**//
                  ->where('folders.is_deleted = ?', 0)
                  ->where($where);

        $row = $this->fetchRow($select);

        return $row;
    }

    /**
     * Returns all the ids of the folder hierarchy marked as accounts_root
     * for a specific user. The ids will be returned in a flat numeric array.
     *
     */
    public function getFoldersForAccountsRoot($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return array();
        }

        $adapter = self::getAdapter();

        $select = self::getFolderBaseQuery($userId)
                  ->where('folders.type=?', 'accounts_root')
                  ->group('folders.id');

        $row = $adapter->fetchRow($select);

        if ($row === false) {
            return array();
        }

        $ids = array();

        $ids[] = $row['id'];

        $this->_getFoldersForAccountsRoot($adapter, $row['id'], $ids);

        return $ids;
    }

    private function _getFoldersForAccountsRoot($adapter, $folderId, &$collectedIds)
    {
        $select = $adapter->select()
                  ->from(array('folders' => self::getTablePrefix() . 'groupware_email_folders'), array(
                      'id'
                  ))
                  ->where('folders.parent_id=?', $folderId);

        $rows = $adapter->fetchAll($select);

        foreach ($rows as $row) {
            $collectedIds[] = $row['id'];
            $this->_getFoldersForAccountsRoot($adapter, $row['id'], $collectedIds);
        }

    }



    /**
     * Returns the base query for reading out folders.
     */
    public static function getFolderBaseQuery($userId)
    {
        $adapter = self::getDefaultAdapter();
        return $adapter->select()
               ->from(array('folders' => self::getTablePrefix() . 'groupware_email_folders'), array(
                 'name',
                 'id',
                 'id AS id_for_path',
                 '(1) AS is_selectable',
                 'is_child_allowed',
                 'is_locked',
                 'type'
               ))
               ->joinLeft(array('childtable' => self::getTablePrefix() . 'groupware_email_folders'),
                'childtable.parent_id=folders.id',
                 array(
                  'child_count' => 'COUNT(DISTINCT childtable.id)'
               ))
               //**//
               ->join(
                   array('folders_users' => self::getTablePrefix() . 'groupware_email_folders_users'),
                   'folders_users.groupware_email_folders_id=folders.id' .
                   ' AND ' .
                   'folders_users.users_id=' . $userId .
                   ' AND ' .
                   'folders_users.relationship=\'owner\'',
                   array()
               )
               //**//
               ->where('folders.is_deleted = ?', 0)
               ->group('folders.id')
               ->order('folders.id ASC');
    }

    /**
     * Returns all root folders for the user. A root folder is either a
     * folder that was created when an account was created, or a folder
     * that was put into public that contains only folders of the type
     * 'folder'.
     * A root folder that is connectec with multiple accounts usually is of the
     * type 'accounts_root', a root-folder that was created for a specific account
     * is usually of the type 'root'. If the folder is of the type 'root' (i.e.
     * was created for a specific account), the name of the folder will
     * default to the name of the account it is connected with.
     * Folders of the type "root_remote" map a remote folder location, i.e.
     * folders which are not managed by conjoon.
     *
     * @return array
     */
    protected function getRootFolders($userId)
    {
        $adapter = self::getDefaultAdapter();
        $select  = self::getFolderBaseQuery($userId)
                ->join(
                      array('pendingfolder' => self::getTablePrefix() . 'groupware_email_folders'),
                      'pendingfolder.id=folders.id',
                      array('pending_count' => '(0)')
                   )
                   ->where('folders.type=?', 'root')
                   ->orWhere('folders.type=?', 'root_remote')
                   ->orWhere('folders.type=?', 'accounts_root');

        $rows = $adapter->fetchAll($select);

        return $rows;
    }

    /**
     * Returns the child-folders for the specified id and userId.
     * If the parentId equals to 0, all root folders for the user
     * will be read out.
     *
     * @return array
     */
    public function getFolders($parentId, $userId)
    {
        $userId   = (int)$userId;
        $parentId = (int)$parentId;

        if ($userId <= 0 || $parentId < 0) {
            return array();
        }

        if ($parentId == 0) {
            return $this->getRootFolders($userId);
        }

        return $this->_getFolders($parentId, $userId, true);
    }

    /**
     * Returns a single folder with all nedded information, including
     * pending count.
     *
     * @param integer $id The id of the folder to fetch.
     * @param integer $userId The id of the user to fetch this folder for.
     *
     * @return Zend_Db_Table_Row
     */
    public function getFolderForId($id, $userId)
    {
        $userId = (int)$userId;
        $id     = (int)$id;

        if ($userId <= 0 || $id < 0) {
            return array();
        }

        if ($id == 0) {
            return $this->getRootFolders($userId);
        }

        return $this->_getFolders($id, $userId, false);
    }

    /**
     * Helper for fetching a single folder or child folders froma given
     * parentId.
     *
     * @param integer $id eitehr the id of the folder to fetch, or the parent
     * id of the folder to fetch the children for. if $isParentId is set to true,
     * child folders for this id will be returned.
     * @param integer $userId
     * @param boolean $isParentId Whether the specified id is the id of a folder
     * to fetch child folders for
     *
     * @return mixed
     */
    private function _getFolders($id, $userId, $isParentId = false)
    {
        $adapter = $this->getAdapter();
        $select  = self::getFolderBaseQuery($userId)
                   ->joinLeft(array(
                    'items' => self::getTablePrefix() . 'groupware_email_items'),
                    'folders.id=items.groupware_email_folders_id',
                     array()
                   )
                   ->joinLeft(
                       array(
                           'flag' => self::getTablePrefix() . 'groupware_email_items_flags'
                       ),
                    'items.id = flag.groupware_email_items_id'.
                    ' AND '.
                    'flag.is_read=0'.
                    ' AND '.
                    'flag.is_deleted=0'.
                    ' AND ' .
                    $adapter->quoteInto('flag.user_id=?', $userId, 'INTEGER'),
                    array('pending_count' => "IF (folders.meta_info !='draft' AND folders.meta_info !='outbox' ,COUNT(DISTINCT flag.groupware_email_items_id), COUNT(DISTINCT items.id))")
                   );

        if ($isParentId === false) {
            $select = $select->where(
                'folders.id = ?', $id
            );

            return $adapter->fetchRow($select);
        } else {
            $select = $select->where(
                'folders.parent_id = ?', $id
            );
            return $adapter->fetchAll($select);
        }
    }

    /**
     * Returns a single folder entry.
     *
     * @param integer $folderId The id of the folder to fetch
     * @param integer $userId The user id for reading the additional data out, such
     * as unread items.
     *
     * @return Zend_Db_Table_Row
     */
    public function getFolderBaseData($folderId)
    {
        $folderId = (int)$folderId;

        if ($folderId <= 0) {
            return null;
        }

        $where = $this->getAdapter()->quoteInto('id = ?', $folderId, 'INTEGER');

        // check first if the folder may get deleted
        $select = $this->select()
                  ->from($this, array('*', 'id AS id_for_path', '(1) as is_selectable'))
                  ->where($where);

        $row = $this->fetchRow($select);

        return $row;
    }

    /**
     * @return intger
     */
    protected function _getDefaultFolderIdForType($accountId, $userId, $type)
    {
        $accountId = (int)$accountId;
        $userId    = (int)$userId;

        if ($accountId == 0 || $userId == 0) {
            return 0;
        }

        $adapter = self::getDefaultAdapter();

        $select = $adapter->select()
                  ->from(self::getTablePrefix() . 'groupware_email_folders', array('folder_id' => 'id'))
                  ->join(
                        array('accounts' => self::getTablePrefix() . 'groupware_email_accounts'),
                        $adapter->quoteInto('accounts.id=?', $accountId, 'INTEGER') .
                        ' AND ' .
                        $adapter->quoteInto('accounts.user_id=?', $userId, 'INTEGER')/* .
                        // REMOVED since information about a mapping might still be needed
                        // in case the account was flagged as deleted, but an item mapped to this
                        // account still exists in the database and is shown in the frontend
                        // in a specific folder
                        ' AND '.
                        'accounts.is_deleted=0'*/,
                        array())
                  ->join(
                        array('folders_accounts' => self::getTablePrefix() . 'groupware_email_folders_accounts'),
                        'folders_accounts.groupware_email_folders_id=' . self::getTablePrefix() . 'groupware_email_folders.id '.
                        ' AND ' .
                        $adapter->quoteInto('folders_accounts.groupware_email_accounts_id=?', $accountId, 'INTEGER'),
                        array())
                  ->where('type = ? ', $type);

        $row = $adapter->fetchRow($select);

        if (!$row || empty($row)) {
            return 0;
        }

        return $row['folder_id'];
    }

    /**
     * Returns the standard mapping for folders. A standard mapping consists of
     * a group of folders where each one is mapped to a specific type, i.e.
     * of the type inbox, sent, draft, trash or outbox. This method does also
     * consider "is_deleted" flagged accounts, since this information may still be
     * needed for items which show up in "accounts_root" folder hierarchies.
     *
     * @param integer $userId The id of the user to fetch informations about
     * his standard mappings for
     *
     * @return array an associative array with the following key/value pairs:
     *     - parent_id: The id of the parent - folder (i.e. the folder with the
     *       type "root" or "accounts_root" for the queried folder
     *     - id: The id of the folder
     *     - groupware_email_accounts_id: The id of teh account for which the folder
     *       was created
     *     - type: the type of the folder, i.e. inbox, outbox, sent, trash, draft
     *
     */
    public function getLocalMappingsForUser($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return array();
        }

        $adapter = self::getDefaultAdapter();

        $select = $adapter->select()
                  ->from(self::getTablePrefix() . 'groupware_email_folders', array(
                      'id', 'parent_id', 'type'
                  ))
                  ->join(
                        array('accounts' => self::getTablePrefix() . 'groupware_email_accounts'),
                        $adapter->quoteInto('accounts.user_id=?', $userId, 'INTEGER'),
                        array('groupware_email_accounts_id' => 'id'))
                  ->join(
                        array('folders_accounts' => self::getTablePrefix() . 'groupware_email_folders_accounts'),
                        'folders_accounts.groupware_email_folders_id=' . self::getTablePrefix() . 'groupware_email_folders.id '.
                        ' AND ' .
                        'folders_accounts.groupware_email_accounts_id=accounts.id',
                        array())
                  ->where('type = "inbox"')
                  ->orWhere('type = "outbox"')
                  ->orWhere('type = "draft"')
                  ->orWhere('type = "sent"')
                  ->orWhere('type = "trash"');

        $rows = $adapter->fetchAll($select);

        if (!$rows || empty($rows)) {
            return array();
        }

        return $rows;
    }


    /**
     * Adds an individual folder hierarchy for an imap account.
     *
     * @param integer $accountId
     * @param integer $userId
     * @param string  $name The name of the account to use as the folders name
     *
     * @return integer The total number of data inserted
     */
    public function createFolderHierarchyForImapAccount($accountId, $userId, $name)
    {
        $accountId = (int)$accountId;
        $userId    = (int)$userId;
        $name      = trim((string)$name);

        if ($accountId == 0 || $userId == 0 || $name == "") {
            return 0;
        }

        $adapter = $this->getAdapter();

        $adapter->beginTransaction();

        $ids = array();

        try {

            // root folder
            $parentId = $this->insert(array(
                'name'             => $name,
                'is_child_allowed' => 0,
                'is_locked'        => 1,
                'type'             => 'root_remote',
                'meta_info'        => 'inbox',
                'parent_id'        => null
            ));
            $ids[] = $parentId;

            /**
             * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers
             */
            require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersUsers.php';

            $foldersUsers = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers();
            $foldersUsers->addRelationship(
                $ids, $userId, Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers::OWNER
            );

            $adapter->commit();

        } catch (Exception $e) {
            $adapter->rollBack();
            return array();
        }

        // map all existing folders from the root hierarchy to the new account
        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersAccounts.php';

        $foldersAccountsModel = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts();

        return $foldersAccountsModel->mapFolderIdsToAccountId($ids, $accountId);
    }

    /**
     * Creates a local folder hierarchy for a single account.
     * Multiple accounts which should be managed in one and teh same hierarchy
     * should be managed by folders created by
     * @see createFolderBaseHierarchyAndMapAccountIdForUserId
     *
     * @param integer $accountId
     * @param integer $userId
     * @param string  $name
     *
     * @return integer The total number of data inserted
     */
    public function createFolderHierarchyAndMapAccountIdForUserId(
        $accountId, $userId, $name) {

        $accountId = (int)$accountId;
        $userId    = (int)$userId;
        $name      = trim((string)$name);

        if ($accountId == 0 || $userId == 0 || $name == "") {
            return 0;
        }

        $folderIds = $this->addLocalRootBaseHierarchy($userId, $name);

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersAccounts.php';

        $foldersAccountsModel = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts();
        return $foldersAccountsModel->mapFolderIdsToAccountId($folderIds, $accountId);
    }


    /**
     * Maps the account id for the user to the users list of folders.
     * This method will check if the user already has a folder
     * root hierarchy. If that is not the case, one will be created.
     *
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return integer The total number of data inserted
     */
    public function createFolderBaseHierarchyAndMapAccountIdForUserId($accountId, $userId)
    {
        $accountId = (int)$accountId;
        $userId    = (int)$userId;

        if ($accountId == 0 || $userId == 0) {
            return 0;
        }

        $folderIds = $this->getFoldersForAccountsRoot($userId);

        if (empty($folderIds)) {
            // user creates his very first email account.
            // create base folder hierarchy and map them to the
            // account later on
            $folderIds = $this->addAccountsRootBaseHierarchy($userId);
        }

        // map all existing folders from the accounts_root hierarchy to the new account
        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersAccounts.php';

        $foldersAccountsModel = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts();
        return $foldersAccountsModel->mapFolderIdsToAccountId($folderIds, $accountId);
    }


// -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Groupware_Email_Folder';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getFolderForId',
            'getFolders',
            'getFolderBaseData',
            'getAnyRootMailFolderBaseData'
        );
    }
}