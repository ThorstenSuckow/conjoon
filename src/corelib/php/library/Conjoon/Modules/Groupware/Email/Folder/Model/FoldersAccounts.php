<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * @see Zend_Db_Table
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Table data gateway. Models the table <tt>groupware_email_folders_accounts</tt>.
 *
 * @uses Zend_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts extends Zend_Db_Table_Abstract {

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_folders_accounts';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = array(
        'groupware_email_folders_id',
        'groupware_email_accounts_id'
    );

    /**
     * Returns all the folder ids for a scpecific account id.
     *
     * @param integer $accountId
     *
     * @return array A numeric array with the folder ids as the values.
     */
    public function getFolderIdsForAccountId($accountId)
    {
        $accountId = (int)$accountId;
        if ($accountId <= 0) {
            return array();
        }

        $rows = $this->fetchAll(
            $this->select()->from($this, array('groupware_email_folders_id'))
                 ->where('groupware_email_accounts_id=?', $accountId)
        );

        $folderIds = array();
        foreach ($rows as $row) {
            $folderIds[] = $row->groupware_email_folders_id;
        }

        return $folderIds;
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
     * Deletes all data for the account with the specified account id.
     * If there are currently other accounts existing which are using the folders
     * the account id is currently using, no data will be deleted.
     *
     * @param integer $id
     *
     * @return integer 0 if no data was deleted, otherwise the number of deleted
     * data
     */
    public function deleteForAccountId($accountId)
    {
        $accountId = (int)$accountId;
        if ($accountId <= 0) {
            return 0;
        }

        $where = $this->getAdapter()->quoteInto(
            'groupware_email_accounts_id = ?', $accountId, 'INTEGER'
        );

        $affected = $this->delete($where);

        return $affected;
    }

    /**
     * Checks whether any of the folderIds as found in the passed numeric
     * array still have a mapping in this table.
     *
     * @param Array $folderIds
     *
     * @return Array Returns an associative array with the key being the folder
     * id, and the value being an array with mapped account ids.
     */
    public function getAccountIdsMappedToFolderIds(Array $folderIds)
    {
        /**
         * @see Conjoon_Filter_PositiveArrayValues
         */
        require_once 'Conjoon/Filter/PositiveArrayValues.php';

        $filter = new Conjoon_Filter_PositiveArrayValues();

        $folderIds = $filter->filter($folderIds);

        if (empty($folderIds)) {
            return array();
        }

        $idStr = implode(",", $folderIds);

        $rows = $this->fetchAll(
            $this->select()->from($this)
                 ->where('groupware_email_folders_id IN ('.$idStr.')')
        );

        $result = array();
        foreach ($rows as $row) {
            $folderId = $row->groupware_email_folders_id;

            if (!array_key_exists($folderId, $result)) {
                $result[$folderId] = array();
            }

            $result[$folderId][] = $row->groupware_email_accounts_id;
        }

        return $result;
    }

    /**
     * Reads out the account ids mapped to the folder with the parent id
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
                'groupware_email_folders_id'  => $id,
                'groupware_email_accounts_id' => $folderAccounts[$i]['groupware_email_accounts_id']
            ));

            $a = $res ? $a+1 : $a;
        }

        return $a;
    }

    /**
     * Maps the specified folder ids in $folderIds to the account id specified
     * in $accountId.
     *
     * @param array $folderIds
     * @param integer $accountId
     *
     * @return integer The total number of data added
     */
    public function mapFolderIdsToAccountId(Array $folderIds, $accountId)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0) {
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
                'groupware_email_folders_id'  => $folderIds[$i],
                'groupware_email_accounts_id' => $accountId
            ));

            $a = $res ? $a+1 : $a;
        }

        return $a;
    }


}