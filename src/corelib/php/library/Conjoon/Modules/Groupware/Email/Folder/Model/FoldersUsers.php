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
 * Table data gateway. Models the table <tt>groupware_email_folders_users</tt>.
 *
 * @uses Zend_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers extends Conjoon_Db_Table {

    /**
     * @const OWNER
     */
    const OWNER = 'owner';


    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_folders_users';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = array(
        'groupware_email_folders_id',
        'users_id'
    );

    protected function _checkRelationshipType($type)
    {
        switch ($type) {
            case self::OWNER:
                return true;
        }

        return false;
    }

    /**
     * Deletes all data for the folder with the specified id.
     *
     * @param integer $id
     *
     * @return integer 0 if no data was deleted, otherwise the number of deleted
     * data
     */
    public function deleteForFolder($id)
    {
        $id = (int)$id;
        if ($id <= 0) {
            return 0;
        }

        $where    = $this->getAdapter()->quoteInto('groupware_email_folders_id = ?', $id, 'INTEGER');
        $affected = $this->delete($where);

        return $affected;
    }

    /**
     * Reads out the user ids mapped to the folder with the parent id
     * and writes this information into the table under the new $id.
     *
     * @param integer $parentId The parent's folder information
     * @param integer $id The new id under which all information previously
     * fetched from $parentId should be written.
     *
     * @return integer The total number of new entires added
     */
    public function inheritFromParentIdForFolderId($parentId, $id)
    {
        $parentId = (int)$parentId;
        $id       = (int)$id;
        if ($parentId <= 0 || $id <= 0) {
            return 0;
        }

        $select = $this
                  ->select()
                  ->where('groupware_email_folders_id = ?', $parentId);

        $folderAccounts = $this->fetchAll($select)->toArray();

        $a = 0;
        for ($i = 0, $len = count($folderAccounts); $i < $len; $i++) {
            $res = $this->insert(array(
                'groupware_email_folders_id' => $id,
                'users_id'                   => $folderAccounts[$i]['users_id'],
                'relationship'               => $folderAccounts[$i]['relationship'],
            ));

            $a = $res ? $a+1 : $a;
        }

        return $a;
    }

    /**
     * Returns the relationship for the specified folder and user.
     *
     * @param integer $folderId
     * @param integer $userId
     *
     * @return string or null
     *
     * @throws Conjoon_ArgumentException
     */
    public function getRelationShipForFolderAndUser($folderId, $userId)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        $data = array('folderId' => $folderId, 'userId' => $userId);

        Conjoon_Argument_Check::check(array(
            'folderId' => array(
                'type'       => 'int',
                'allowEmpty' => false
            ),
            'userId' => array(
                'type'       => 'int',
                'allowEmpty' => false
            ),
        ), $data);

        $folderId = $data['folderId'];
        $userId   = $data['userId'];

        $select = $this->select()
            ->from($this, array('relationship'))
            ->where($this->getAdapter()->quoteInto(
                'groupware_email_folders_id = ?', $folderId, 'INTEGER'
            ))
            ->where($this->getAdapter()->quoteInto(
                'users_id = ?', $userId, 'INTEGER'
            ));

        $row = $this->fetchRow($select);

        if (!$row) {
            return null;
        }

        return $row->relationship == 'owner'
               ? self::OWNER
               : null;
    }


    /**
     * Adss a relationship for the specified folder ids and the $userId to this table.
     *
     * @param Array $folderIds
     * @param integer $userId
     * @param string $relationshipType
     *
     * @return integer The total number of added data
     */
    public function addRelationship(Array $folderIds, $userId, $relationshipType)
    {
        $userId = (int)$userId;
        if ($userId <= 0) {
            return 0;
        }

        if (!$this->_checkRelationshipType($relationshipType)) {
            return 0;
        }

        /**
         * @see Conjoon_Filter_PositiveArrayValues
         */
        require_once 'Conjoon/Filter/PositiveArrayValues.php';

        $filter = new Conjoon_Filter_PositiveArrayValues();

        $folderIds = $filter->filter($folderIds);

        if (empty($folderIds)) {
            return 0;
        }

        $a = 0;
        for ($i = 0, $len = count($folderIds); $i < $len; $i++) {
            $res = $this->insert(array(
                'groupware_email_folders_id' => $folderIds[$i],
                'users_id'                   => $userId,
                'relationship'               => $relationshipType
            ));
            $a = $res ? $a+1 : $a;
        }
        return $a;
    }

}