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
 * Zend_Db_Table
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Intrabuild_BeanContext_Decoratable
 */
require_once 'Intrabuild/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>groupware_email_items</tt>.
 *
 *
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Item_Model_Item
    extends Zend_Db_Table_Abstract implements Intrabuild_BeanContext_Decoratable {


    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_items';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

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
    public function getTotalItemCount($folderId, $userId)
    {
        $folderId = (int)$folderId;
        $userId   = (int)$userId;

        if ($folderId <= 0 || $userId <= 0) {
            return 0;
        }

        $select = $this->select()
                  ->from($this, array(
                    'COUNT(id) as count_id'
                  ))
                  ->join(
                        array('flags' => 'groupware_email_items_flags'),
                        'flags.groupware_email_items_id=groupware_email_items.id '.
                        ' AND ' .
                        'flags.is_deleted=0 '.
                        'AND '.
                        $this->getAdapter()->quoteInto('flags.user_id=?', $userId, 'INTEGER'),
                        array())
                 ->where('groupware_email_folders_id = ?', $folderId);

        $row = $this->fetchRow($select);

        return ($row !== null) ? $row->count_id : 0;

    }

    /**
     * Applies the correct table alias to the passed fieldname
     *
     */
    private function getTableWithSortField($field)
    {
         switch (trim(strtolower($field))) {
            case 'id':
                return 'items.id';
            case 'is_attachment':
                return 'is_attachment';
            case 'is_read':
                return 'is_read';
            case 'cc':
                return 'items.cc';
            case 'date':
                return 'items.date';
            case 'to':
                return 'items.to';
            case 'to':
                return 'items.to';
            case 'subject':
                return 'items.subject';
            case 'from':
                return 'items.from';
            case 'is_spam':
                return 'is_spam';
            default:
                return 'items.subject';
        }

    }


    /**
     * Returns the base query for email-items
     *
     * @return Zend_Db_Table_Select
     */
    public static function getItemBaseQuery($userId, array $sortInfo = null)
    {
        if ($sortInfo === null) {
            $sortInfo = array();
        }

        $adapter = self::getDefaultAdapter();

        $select= $adapter->select()
                ->from(array('items' => 'groupware_email_items'),
                  array(
                      'id',
                      'cc',
                      'to',
                      'subject',
                      'from',
                      'date',
                      'groupware_email_folders_id'
                ))
                ->join(
                    array('flag' => 'groupware_email_items_flags'),
                    '`flag`.`groupware_email_items_id` = `items`.`id`' .
                    ' AND '.
                    $adapter->quoteInto('`flag`.`user_id`=?', $userId, 'INTEGER').
                    ' AND '.
                    '`flag`.`is_deleted`=0',
                    array('is_spam', 'is_read')
                )
                ->joinLeft(
                    array('folders' => 'groupware_email_folders'),
                    '`folders`.`id` = `items`.`groupware_email_folders_id`',
                    array('is_draft' => $adapter->quoteInto('(`folders`.`meta_info` = ?)', 'draft', 'STRING'))
                )
                ->joinLeft(
                    array('attachments' => 'groupware_email_items_attachments'),
                    '`attachments`.`groupware_email_items_id` = `items`.`id`',
                    array('is_attachment' => '(COUNT(DISTINCT `attachments`.`id`) > 0)')
                )
                ->group('items.id');

        if (!empty($sortInfo)) {
            return $select->order(self::getTableWithSortField($sortInfo['sort']).' '.$sortInfo['dir'])
                          ->limit($sortInfo['limit'], $sortInfo['start']);
        }

        return $select;

    }

    /**
     * Fetches the email items for the specified user for the specified folder.
     *
     *
     * @param integer $userId The id of the user
     * @param integer $folderId The id of the folder
     * @param array $sortInfo An array with sortInfo.
     *
     * @return array
     */
    public function getEmailItemsFor($userId, $folderId, Array $sortInfo)
    {
        if ((int)$userId <= 0 || (int)$folderId <= 0) {
            return array();
        }

        // fetch the requested range of email items
        $adapter = $this->getAdapter();

        $select = self::getItemBaseQuery($userId, $sortInfo)
                  ->where('`items`.`groupware_email_folders_id` = ?', $folderId);


        $rows = $adapter->fetchAll($select);

        if ($rows != false) {
            return $rows;
        }

        return array();
    }

    /**
     * Moves all items with the specified ids to the folder with the specified
     * folderId
     *
     * @param array $itemIds A numeric array with all id's of the items that get moved
     * @param integer $groupwareEmailFoldersId The id of the target folder the items get
     * moved to.
     *
     * @return integer The number of total rows updated
     */
    public function moveItemsToFolder(Array $itemIds, $groupwareEmailFoldersId)
    {
        $groupwareEmailFoldersId = (int)$groupwareEmailFoldersId;

        if ($groupwareEmailFoldersId <= 0) {
            return 0;
        }

        $clearedItemIds = array();
        $cc = 0;
        for ($i = 0, $len = count($itemIds); $i < $len; $i++) {
            $id = (int)$itemIds[$i];
            if ($id > 0) {
                $clearedItemIds[] = $id;
                $cc++;
            }
        }
        if ($cc == 0) {
            return 0;
        }

        $data = array('groupware_email_folders_id' => $groupwareEmailFoldersId);
        $adapter = $this->getAdapter();
        return $this->update($data, array(
            'id IN ('.implode(',', $clearedItemIds).')'
        ));
    }

    /**
     * Deletes all items and the corresponding data  for the specified folder.
     *
     * @param integer $id The id of the folder for which the data gets deleted.
     * @param integer $userId The id of the user to delete the data for
     *
     * @return integer The number of items deleted
     */
    public function deleteItemsForFolder($id, $userId)
    {
        $id     = (int)$id;
        $userId = (int)$userId;
        if ($id <= 0 || $userId <= 0) {
            return 0;
        }

        // fetch all the ids from the email items belonging to the specified
        // folder
        $rows = $this->fetchAll(
            $this->select()->from($this, array('id'))
                 ->where('groupware_email_folders_id = ?', $id)
        );

        $itemIds = array();

        foreach ($rows as $row) {
            $itemIds[] = $row->id;
        }

        return $this->deleteItemsForUser($itemIds, $userId);
    }

    /**
     * Deletes all items with the specified ids for the specified user.
     * The items will only be deleted if all rows in groupware_email_items_flags
     * for the corresponding item have been set to is_deleted=1. Otherwise,
     * only the is_deleted field for the specified user will be set to 1.
     *
     * @param array $itemIds A numeric array with all id's of the items that are
     * about to be deleted
     * @param integer $userId The id of the user for which the items get deleted.
     *
     * @return integer The number of total rows deleted
     */
    public function deleteItemsForUser(Array $itemIds, $userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return 0;
        }

        $clearedItemIds = array();
        $cc = 0;
        for ($i = 0, $len = count($itemIds); $i < $len; $i++) {
            $id = (int)$itemIds[$i];
            if ($id > 0) {
                $clearedItemIds[] = $id;
                $cc++;
            }
        }
        if ($cc == 0) {
            return 0;
        }

        require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Flag.php';
        $flagModel = new Intrabuild_Modules_Groupware_Email_Item_Model_Flag();
        $flagModel->flagItemsAsDeleted($clearedItemIds, $userId);

        $deleteValues = $flagModel->areItemsFlaggedAsDeleted($clearedItemIds);

        // if the second argument to array_filter is ommited, array_filter gets
        // all keys which values does not equal to false
        $itemsToDelete = array_filter($deleteValues);

        $idString = implode(',', array_keys($itemsToDelete));
        $deleted = $this->delete('id IN ('.$idString.')');

        if ($deleted > 0) {

            require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Inbox.php';
            require_once 'Intrabuild/Modules/Groupware/Email/Attachment/Model/Attachment.php';

            $inboxModel      = new Intrabuild_Modules_Groupware_Email_Item_Model_Inbox();
            $attachmentModel = new Intrabuild_Modules_Groupware_Email_Attachment_Model_Attachment();

            $flagModel->delete('user_id = '.$userId.' AND groupware_email_items_id IN ('.$idString.')');
            $attachmentModel->delete('groupware_email_items_id IN ('.$idString.')');
            $inboxModel->delete('groupware_email_items_id IN ('.$idString.')');
        }

        return $deleted;
    }

// -------- interface Intrabuild_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Intrabuild_Modules_Groupware_Email_Item';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getEmailItemsFor'
        );
    }

}