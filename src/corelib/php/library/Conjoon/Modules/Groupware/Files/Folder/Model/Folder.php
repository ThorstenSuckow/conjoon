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
 * Table data gateway. Models the table <tt>groupware_files_folders</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Files
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Files_Folder_Model_Folder extends Conjoon_Db_Table {

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_files_folders';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Returns the base temp folder id for the specified user, i.e. the folder
     * with type "temp".
     * Returns 0 if the folder could not be found
     *
     * @param integer $userId
     *
     * @return integer
     *
     * @throws InvalidArgumentException
     */
    public function getTempFolderIdForUser($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - $userId"
            );
        }

        return $this->_getDefaultFolderIdForType($userId, 'temp');
    }

    /**
     * Creates the temp folder for the specified user id.
     *
     * @param integer $userId
     *
     * @return integer
     *
     * @throws InvalidArgumentException
     */
    public function createTempFolderForUser($userId)
    {
        return $this->addFolder($userId, 'temp', 0, 'temp', true, true);
    }

    /**
     * Adds a folder to the specified folder id.
     *
     * @param integer $userId
     * @param string  $name
     * @param integer $parentId
     *
     * @return integer
     *
     * @throws InvalidArgumentException
     */
    public function addFolder($userId, $name, $parentId, $type, $isLocked = false,
        $isChildAllowed = true)
    {
        $userId = (int)$userId;
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - $userId"
            );
        }

        $name = trim((string)$name);
        if ($name == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied for name - $name"
            );
        }

        $parentId = (int)$parentId;
        if ($parentId < 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for parentId - $parentId"
            );
        }

        // check if a folder does already exists with the same name
        // check now if a folder with the same name already exists
        if ($this->getFolderIdForName($name, $parentId) != 0) {
            return 0;
        }

        $data = array(
            'name'             => $name,
            'is_child_allowed' => $isChildAllowed ? 1 : 0,
            'is_locked'        => $isLocked ? 1 : 0,
            'type'             => $type,
            'parent_id'        => $parentId
        );

        $id = (int)$this->insert($data);

        if ($id <= 0) {
            return 0;
        }

        /**
         * @see Conjoon_Modules_Groupware_Files_Folder_Model_FoldersUsers
         */
        require_once 'Conjoon/Modules/Groupware/Files/Folder/Model/FoldersUsers.php';
        $foldersUsersModel = new Conjoon_Modules_Groupware_Files_Folder_Model_FoldersUsers();

        if ($parentId != 0) {
            $foldersUsersModel->inheritFromParentIdForFolderId($parentId, $id);
        } else {
            $foldersUsersModel->addRelationship(array($id), $userId,
                Conjoon_Modules_Groupware_Files_Folder_Model_FoldersUsers::OWNER
            );
        }

        return $id;

    }

    /**
     * Returns the id of the folder with the the sppecified name for the specified
     * parent folder. Returns 0 if no wolder was found.
     *
     * @param string $name
     * @param integer $parentId
     *
     * @return integer
     *
     * @throws InvalidArgumentException
     */
    public function getFolderIdForName($name, $parentId)
    {
        $parentId = (int)$parentId;
        if ($parentId < 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for parentId - $parentId"
            );
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
            return (int)$row->id;
        }

        return 0;
    }

    /**
     * @return integer
     *
     * @throws InvalidArgumentException
     */
    protected function _getDefaultFolderIdForType($userId, $type)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - $userId"
            );
        }

        $adapter = self::getDefaultAdapter();

        $select = $adapter->select()
                  ->from(self::getTablePrefix() . 'groupware_files_folders', array('folder_id' => 'id'))
                  ->join(
                        array('folderUsers' => self::getTablePrefix() . 'groupware_files_folders_users'),
                        $adapter->quoteInto('folderUsers.users_id=?', $userId, 'INTEGER') .
                        ' AND ' .
                        $adapter->quoteInto('folderUsers.relationship=?', 'owner', 'STRING') .
                        ' AND '.
                        'folderUsers.groupware_files_folders_id=id',
                        array())
                  ->where('type = ? ', $type);

        $row = $adapter->fetchRow($select);

        if (!$row || empty($row)) {
            return 0;
        }

        return (int)$row['folder_id'];
    }


}