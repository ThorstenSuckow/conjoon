<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
 * Table data gateway. Models the table <tt>groupware_files_folders_users</tt>.
 *
 * @uses Zend_Db_Table
 * @package Conjoon_Groupware_Files
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Files_Folder_Model_FoldersUsers extends Conjoon_Db_Table {

    /**
     * @const OWNER
     */
    const OWNER = 'owner';


    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_files_folders_users';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = array(
        'groupware_files_folders_id',
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
     * Ads a relationship for the specified folder ids and the $userId to this table.
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
                'groupware_files_folders_id' => $folderIds[$i],
                'users_id'                   => $userId,
                'relationship'               => $relationshipType
            ));
            $a = $res ? $a+1 : $a;
        }
        return $a;
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
     *
     * @throws InvalidArgumentException
     */
    public function inheritFromParentIdForFolderId($parentId, $id)
    {
        $parentId = (int)$parentId;
        $id       = (int)$id;
        if ($parentId < 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for parentId - $parentId"
            );
        }

        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for id - $id"
            );
        }

        $select = $this
                  ->select()
                  ->where('groupware_files_folders_id = ?', $parentId);

        $folders = $this->fetchAll($select)->toArray();

        $a = 0;
        for ($i = 0, $len = count($folders); $i < $len; $i++) {
            $res = $this->insert(array(
                'groupware_files_folders_id' => $id,
                'users_id'                   => $folders[$i]['users_id'],
                'relationship'               => $folders[$i]['relationship'],
            ));

            $a = $res ? $a+1 : $a;
        }

        return $a;
    }

}