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
 * @see Zend_Date
 */
require_once 'Zend/Date.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Keys
 */
require_once 'Conjoon/Modules/Groupware/Email/Keys.php';

/**
 * @see Conjoon_Util_Array
 */
require_once 'Conjoon/Util/Array.php';

/**
 * @see Conjoon_Filter_EmailRecipients
 */
require_once 'Conjoon/Filter/EmailRecipients.php';

/**
 * @see Conjoon_Filter_EmailRecipientsToString
 */
require_once 'Conjoon/Filter/EmailRecipientsToString.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Address
 */
require_once 'Conjoon/Modules/Groupware/Email/Address.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Folder_Model_Folder
 */
require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Model_References
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Model/References.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Model_Flag
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Flag.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Model_Inbox
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Inbox.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Model_Outbox
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Outbox.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment
 */
require_once 'Conjoon/Modules/Groupware/Email/Attachment/Model/Attachment.php';

/**
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';

/**
 * Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>groupware_email_items</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Item_Model_Item
    extends Conjoon_Db_Table implements Conjoon_BeanContext_Decoratable {


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
     * @var array
     */
    protected $cachedFolderPaths;

    /**
     * @var Conjoon_Modules_Groupware_Email_Folder_Model_Folder
     */
    protected $folderModel;

    /**
     * Applies the correct table alias to the passed fieldname
     *
     */
    private static function getTableWithSortField($field)
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
            case 'recipients':
                return 'items.recipients';
            case 'sender':
                return 'items.sender';
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
     * Looks up if the specified id for the specified user has an entry in
     * groupware_email_items_references has an entry and returns the item
     * with the corresponding id
     *
     * @param integer $itemId
     * @param integer $userId
     *
     * @return array
     */
    public function getReferencedItem($itemId, $userId)
    {
        $itemId = (int)$itemId;
        $userId = (int)$userId;

        if ($itemId <= 0 || $userId <= 0) {
            return array();
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Model_References
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Model/References.php';

        $refModel = new Conjoon_Modules_Groupware_Email_Item_Model_References();

        $row = $refModel->fetchRow(
            $refModel->select()->from($refModel, array('reference_items_id'))
                        ->where('groupware_email_items_id = ? ', $itemId)
                        ->where('user_id = ? ', $userId)
        );

        if (!$row || empty($row)) {
            return array();
        }

        return $this->getItemForUser($row->reference_items_id, $userId);
    }

    /**
     * Returns the email items with the specified ids for the specified user.
     *
     * @param array $itemIds A numeric array with all ids of the email items to
     * fetch
     * @param integer $userId The id of the user
     * @param array $sortInfo optional, an array with sort configuration
     *
     * @return array
     */
    public function getItemsForUser(Array $itemIds, $userId, Array $sortInfo = array())
    {

        $itemIds = empty($itemIds) ? false : $itemIds;
        $userId  = (int)$userId;

        if (!$itemIds || $userId <= 0) {
            return array();
        }

        $sanitizedItemIds = array();

        for ($i = 0, $len = count($itemIds); $i < $len; $i++) {
            $id = (int)$itemIds[$i];

            if ($id <= 0) {
                continue;
            }

            $sanitizedItemIds[] = $id;
        }

        $sanitizedItemIds = array_unique($sanitizedItemIds);

        if (count($sanitizedItemIds) == 0) {
            return array();
        }

        $adapter = $this->getAdapter();

        $select = self::getItemBaseQuery($userId, $sortInfo)
                  ->where('items.id IN ('.implode(',', $sanitizedItemIds).')');

        $rows = $adapter->fetchAll($select);

        if ($rows != false) {

            $rows = $this->applyPathToEmailItems($rows);

            return $rows;
        }

        return array();
    }

    /**
     * Updates the reference table for a referenced message without the id of
     * the message that triggered the reference. This is for messages which
     * are only available on remote servers which do not have an id.
     *
     * @param integer $messageId
     * @param integer $localFolder
     * @param integer $userId
     */
    public function updateReferenceFromARemoteItem(
        $messageId, $localFolder, $userId, $type)
    {
        $messageId   = (int)$messageId;
        $localFolder = (int)$localFolder;

        if ($messageId <= 0 || $localFolder <= 0) {
            return false;
        }

        $select = $this->select()
            ->from($this)
            ->where('`groupware_email_folders_id` = ?', $localFolder)
            ->where('`id` = ?', $messageId);

        $row = $this->fetchRow($select);

        if (!$row) {
            return false;
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Model_References
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Model/References.php';

        $referenceModel = new Conjoon_Modules_Groupware_Email_Item_Model_References();


        $select = $this->select()
            ->from($this)
            ->where('`groupware_email_folders_id` = ?', $localFolder)
            ->where('`id` = ?', $messageId);

        $row = $this->fetchRow($select);

        if (!$row) {
            return false;
        }

        $adapter = $this->getAdapter();

        $select = $adapter->select()
                  ->from(
                    array('references' => self::getTablePrefix()
                                          . 'groupware_email_items_references'
                    ))
                  ->where('`reference_type` = ?', $type)
                  ->where('`reference_items_id` = ?', $messageId)
                  ->where('`user_id` = ?', $userId)
                  ->where('`groupware_email_items_id` = ?', 0);

        $row = $adapter->fetchRow($select);

        if ($row) {
            return true;
        }

        $referenceModel->insert(array(
            'user_id'            => $userId,
            'reference_type'     => $type,
            'reference_items_id' => $messageId
        ));

        return true;
    }

    /**
     * Returns a single item with the specified id for the specified user.
     * Returns an empty array if entry was not found.
     *
     * @param integer $itemId
     * @param integer $userId
     *
     * @return array
     */
    public function getItemForUser($itemId, $userId)
    {
        $itemId = (int)$itemId;
        $userId = (int)$userId;

        if ($itemId <= 0 || $userId <= 0) {
            return array();
        }

        $adapter = $this->getAdapter();

        $where   = $adapter->quoteInto('items.id = ?', $itemId, 'INTEGER');

        $select = self::getItemBaseQuery($userId)
                  ->where($where);

        $row = $adapter->fetchRow($select);

        if (!$row) {
            return array();
        }

        $rows = $this->applyPathToEmailItems(array($row));

        return $rows[0];
    }

    /**
     * Returns the base query for email-items
     *
     * For some queries it can be crucial to add tables which get joined at the beginning
     * of the statement. The optional argument $addTable allows for adding INNER JOIN sources
     * to the beginning of the statement. It keys are "name" which holds a value as specified
     * in the "from" method from Zend_Db_Select, and "cols".
     * The base query will consider owner relationships for the folder the item sits in.
     *
     * @param integer $userId
     * @param array $sortInfo
     * @param array $addTable
     *
     * @return Zend_Db_Table_Select
     */
    public static function getItemBaseQuery($userId, array $sortInfo = null, $addTable = null)
    {
        if ($sortInfo === null) {
            $sortInfo = array();
        }

        $adapter = self::getDefaultAdapter();

        $select= $adapter->select()
                ->from(array('items' => self::getTablePrefix() . 'groupware_email_items'),
                  array(
                      'id',
                      'recipients',
                      'subject',
                      'sender',
                      'date',
                      'groupware_email_folders_id'
                ));

        if (is_array($addTable)) {
            $select = $select->from($addTable['name'], $addTable['cols']);
        }

        $select =
                $select->join(
                    array('folders' => self::getTablePrefix() . 'groupware_email_folders'),
                    '`folders`.`id` = `items`.`groupware_email_folders_id`',
                    array(
                        'is_draft' => $adapter->quoteInto('(`folders`.`meta_info` = ?)', 'draft', 'STRING'),
                        'is_outbox_pending' => $adapter->quoteInto('(`folders`.`meta_info` = ?)', 'outbox', 'STRING')
                    )
                )
                ->join(
                    array('folders_users' => self::getTablePrefix() . 'groupware_email_folders_users'),
                    '`folders_users`.`groupware_email_folders_id` = `items`.`groupware_email_folders_id` '
                    .' AND '
                    .$adapter->quoteInto('`folders_users`.`users_id`=?', $userId, 'INTEGER')
                    .' AND '
                    .$adapter->quoteInto('`folders_users`.`relationship`=?', 'owner', 'STRING'),
                    array()
                )
                ->join(
                    array('flag' => self::getTablePrefix() . 'groupware_email_items_flags'),
                    '`flag`.`groupware_email_items_id` = `items`.`id`' .
                    ' AND '.
                    $adapter->quoteInto('`flag`.`user_id`=?', $userId, 'INTEGER').
                    ' AND '.
                    '`flag`.`is_deleted`=0',
                    array('is_spam', 'is_read')
                )
                ->joinLeft(
                        array('reference' => self::getTablePrefix() . 'groupware_email_items_references'),
                        'reference.reference_items_id=items.id '.
                        ' AND ' .
                        $adapter->quoteInto('reference.is_pending=?', 0, 'INTEGER') .
                        ' AND '.
                        $adapter->quoteInto('reference.user_id=?', $userId, 'INTEGER'),
                        array('referenced_as_types' => 'GROUP_CONCAT(DISTINCT reference.reference_type SEPARATOR \',\')' )
                 )
                ->joinLeft(
                    array('attachments' => self::getTablePrefix() . 'groupware_email_items_attachments'),
                    '`attachments`.`groupware_email_items_id` = `items`.`id`',
                    array('is_attachment' => 'IF(`attachments`.`id` IS NULL, 0, 1)')
                )
                ->group('items.id');

        if (!empty($sortInfo)) {
            $select = $select->order(self::getTableWithSortField($sortInfo['sort']).' '.$sortInfo['dir']);

            if (isset($sortInfo['limit']) && isset($sortInfo['start'])) {
                $select = $select->limit($sortInfo['limit'], $sortInfo['start']);
            }
        }

        return $select;
    }

    /**
     * Returns the total number of email items in this folder.
     *
     * @param integer $folderId
     *
     * @return integer
     */
    public function getEmailItemCountForFolder($folderId)
    {
        $folderId = (int)$folderId;

        if ($folderId <= 0) {
            return 0;
        }

        $select = $this->select()
                  ->from($this, array('count' => 'count(id)'))
                  ->where('`groupware_email_folders_id` = ?', $folderId);

        $result = $this->fetchRow($select);

        if (!$result || empty($result)) {
            return 0;
        }

        return (int)$result->count;
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
            $rows = $this->applyPathToEmailItems($rows);

            return $rows;
        }

        return array();
    }

    /**
     * Applies the folder path to the email items
     *
     * @param array $rows
     */
    public function applyPathToEmailItems(array $items)
    {
        $cachedFolderPaths =& $this->cachedFolderPaths;

        if (!$this->folderModel) {
            $this->folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();
        }

        for ($i = 0, $len = count($items); $i < $len; $i++) {
            $id = $items[$i]['groupware_email_folders_id'];

            if (!isset($cachedFolderPaths[$id]))  {
                $path = $this->folderModel->getPathForFolderId($id);
                $cachedFolderPaths[$id] = $path;
            }

            $items[$i]['path'] = $cachedFolderPaths[$id];
        }

        return $items;
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
     * If that is the case and the item gets entirely deleted, the method will
     * use the accounts-model to check whether there are any accounts flagged
     * as "is_deleted = 1" and also remove this accounts if no more items
     * are exiting in the data storage.
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

        $referenceModel = new Conjoon_Modules_Groupware_Email_Item_Model_References();
        // delete all references for the items for the specified user
        $referenceModel->delete('user_id = '.$userId.' AND reference_items_id IN ('.implode(',', $clearedItemIds).')');

        $flagModel = new Conjoon_Modules_Groupware_Email_Item_Model_Flag();
        $flagModel->flagItemsAsDeleted($clearedItemIds, $userId);

        $deleteValues = $flagModel->areItemsFlaggedAsDeleted($clearedItemIds);

        // if the second argument to array_filter is ommited, array_filter gets
        // all keys which values does not equal to false
        $itemsToDelete = array_filter($deleteValues);

        $idString = implode(',', array_keys($itemsToDelete));
        $deleted = $this->delete('id IN ('.$idString.')');

        if ($deleted > 0) {
            $referencesModel = new Conjoon_Modules_Groupware_Email_Item_Model_References();
            $inboxModel      = new Conjoon_Modules_Groupware_Email_Item_Model_Inbox();
            $outboxModel     = new Conjoon_Modules_Groupware_Email_Item_Model_Outbox();
            $attachmentModel = new Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment();

            /**
             * @see Conjoon_Modules_Groupware_Email_Account_Model_Account
             */
            require_once 'Conjoon/Modules/Groupware/Email/Account/Model/Account.php';

            $accountModel    = new Conjoon_Modules_Groupware_Email_Account_Model_Account();

            $referencesModel->delete('is_pending=1 AND user_id = '.$userId.' AND groupware_email_items_id IN ('.$idString.')');
            $flagModel->delete('user_id = '.$userId.' AND groupware_email_items_id IN ('.$idString.')');
            $attachmentModel->delete('groupware_email_items_id IN ('.$idString.')');
            $inboxModel->delete('groupware_email_items_id IN ('.$idString.')');
            $outboxModel->delete('groupware_email_items_id IN ('.$idString.')');
            $accountModel->removeAsDeletedFlaggedAccounts($userId);
        }

        return $deleted;
    }

    /**
     * Saves a draft into the database. The draft can either already be existing -
     * in case the passed id id available in groupware_email_items_outbox - or new, in
     * which case a whole new record will be created.
     *
     * @param Conjoon_Modules_Groupware_Email_Draft $draft The draft object to save
     * @param Conjoon_Modules_Groupware_Email_Account $account The account which was used writing
     * the draft
     * @param integer $userId The id of the user for whom the draft gets saved.
     * @param string $type The context the draft was created in. If this is reply or reply_all,
     * the id stored in referencesId will be stored in the references-table
     * @param integer $referencesId The id of the message that was referenced creating the draft
     * @param array $postedAttachments A list of attachments, key/value pairs according to
     * com.conjoon.cudgets.data.FileRecord
     * @param array a list of attachment ids to remove from a list of existing attachments
     * belonging to the draft
     * @param array $attachmentMap An reference to an array which will map all attachments
     * that have beens stored
     *
     * @return array the data from groupware_email_item associated with
     * the newly saved entry
     */
    public function saveDraft(Conjoon_Modules_Groupware_Email_Draft $draft,
                              Conjoon_Modules_Groupware_Email_Account $account,
                              $userId, $type = '', $referencesId = -1,
                              $postedAttachments = array(),
                              $removeAttachmentIds = array(),
                              &$attachmentMap = array())
    {
        $emailRecipientsToStringFilter = new Conjoon_Filter_EmailRecipientsToString();
        $emailRecipientsFilter         = new Conjoon_Filter_EmailRecipients();

        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();
        $outboxModel = new Conjoon_Modules_Groupware_Email_Item_Model_Outbox();

        // prepare data to insert or update
        $outboxUpdate = array(
            'sent_timestamp'              => 0,
            'raw_header'                  => '',
            'raw_body'                    => '',
            'groupware_email_accounts_id' => $account->getId()
        );

        // get the draft folder. If the folder's meta info is already of type "draft",
        // no changes will be made to it, otherwise the id of the default sent folder
        // will be used
        $draftFolderId = $draft->getGroupwareEmailFoldersId();
        $info = $folderModel->getMetaInfo($draftFolderId);
        if ($info != Conjoon_Modules_Groupware_Email_Folder_Model_Folder::META_INFO_DRAFT) {
            $draftFolderId = $folderModel->getDraftFolderId($account->getId(), $userId);
            if ($draftFolderId == 0) {
                return null;
            }
        }

        $date = new Zend_Date($draft->getDate());

        $to           = $draft->getTo();
        $cc           = $draft->getCc();
        $bcc          = $draft->getBcc();
        $fromAddress  = new Conjoon_Modules_Groupware_Email_Address(
            array($account->getAddress(), $account->getUserName())
        );

        $toString     = array();
        foreach ($to as $recipient) {
            $toString[] = $recipient->__toString();
        }
        $toString = implode(', ', $toString);

        $ccString     = array();
        foreach ($cc as $recipient) {
            $ccString[] = $recipient->__toString();
        }
        $ccString = implode(', ', $ccString);

        $bccString = array();
        foreach ($bcc as $recipient) {
            $bccString[] = $recipient->__toString();
        }
        $bccString = implode(', ', $bccString);

        /**
         * @see Conjoon_Filter_DateToUtc
         */
        require_once 'Conjoon/Filter/DateToUtc.php';

        $toUtcFilter = new Conjoon_Filter_DateToUtc();

        $itemUpdate = array(
            'date'                       => $toUtcFilter->filter(
                                                $date->get(Zend_Date::ISO_8601)
                                            ),
            'subject'                    => $draft->getSubject(),
            'from'                       => $fromAddress->__toString(),
            'reply_to'                   => $account->getReplyAddress(),
            'to'                         => $toString,
            'cc'                         => $ccString,
            'bcc'                        => $bccString,
            'sender'                     => $emailRecipientsToStringFilter->filter(
                $emailRecipientsFilter->filter(array(
                    $fromAddress->__toString()
                ))
            ),
            'recipients'                 => $emailRecipientsToStringFilter->filter(
                $emailRecipientsFilter->filter(array(
                    $toString,
                    $ccString,
                    $bccString
                ))
            ),
            'references'                 => $draft->getReferences(),
            'in_reply_to'                => $draft->getInReplyTo(),
            'content_text_html'          => $draft->getContentTextHtml(),
            'content_text_plain'         => $draft->getContentTextPlain(),
            'groupware_email_folders_id' => $draftFolderId
        );

        // check if we have to update a record or insert a record.
        $id = $draft->getId();

        $adapter = $this->getAdapter();
        $adapter->beginTransaction();

        try {
            if ($id > 0) {
                // simply update
                $itemWhere = $this->getAdapter()->quoteInto('id = ?', $id);
                $this->update($itemUpdate, $itemWhere);

                $this->saveAttachmentsForDraft(
                    $draft, $postedAttachments, $removeAttachmentIds, $attachmentMap
                );

                $outboxWhere = $outboxModel->getAdapter()->quoteInto('groupware_email_items_id = ?', $id);
                $outboxModel->update($outboxUpdate, $outboxWhere);
            } else {
                // insert!
                $id = $this->insert($itemUpdate);

                if ($id <= 0) {
                    return null;
                }

                $draft->setId($id);

                $this->saveAttachmentsForDraft(
                    $draft, $postedAttachments, $removeAttachmentIds, $attachmentMap
                );

                Conjoon_Util_Array::apply($outboxUpdate, array(
                    'groupware_email_items_id' => $id
                ));
                $outboxModel->insert($outboxUpdate);
                $flagUpdate = array(
                    'groupware_email_items_id' => $id,
                    'user_id'                  => $userId,
                    'is_read'                  => 1,
                    'is_spam'                  => 0,
                    'is_deleted'               => 0
                );

                $flagModel = new Conjoon_Modules_Groupware_Email_Item_Model_Flag();

                $flagModel->insert($flagUpdate);

                // check if the draft references an existing email and if it is in context
                // reply or reply_all
                switch ($type) {
                    case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY:
                    case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_FORWARD:
                    case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY_ALL:
                        if ($referencesId > 0) {
                            $referencesUpdate = array(
                                'groupware_email_items_id' => $id,
                                'user_id'                  => $userId,
                                'reference_items_id'       => $referencesId,
                                'reference_type'           => $type,
                                'is_pending'               => 1
                            );
                            $referencesModel = new Conjoon_Modules_Groupware_Email_Item_Model_References();
                            $referencesModel->insert($referencesUpdate);
                        }
                    break;
                }

            }

            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();

            /**
             * @see Conjoon_Log
             */
            require_once 'Conjoon/Log.php';

            Conjoon_Log::log($e, Zend_Log::ERROR);

            return null;
        }


        return $this->getItemForUser($id, $userId);
    }

    /**
     * Saves a draft and moves it into the outbox folder. The draft can either
     * already be existing - in case the passed id id available in
     * groupware_email_items_outbox - or new, in which case a whole new record
     * will be created.
     * If the type is anything but new or an empty string, the referenced message
     * will be saved in the reference-table, but still with the flag is_pending
     * set to true, which will be set to false once the email is sent.
     *
     * @param Conjoon_Modules_Groupware_Email_Draft $draft The draft object to save
     * @param Conjoon_Modules_Groupware_Email_Account $account The account which was used writing
     * the draft
     * @param integer $userId The id of the user for whom the draft gets saved.
     * @param string $type The context in which the draft was moved to the outbox,
     * either 'reply', 'reply_all', 'forward' or 'new' (or empty string)
     * @param array $postedAttachments A list of attachments, key/value pairs according to
     * com.conjoon.cudgets.data.FileRecord
     * @param array a list of attachment ids to remove from a list of existing attachments
     * belonging to the draft
     *
     * @return array the data from groupware_email_item associated with
     * the newly saved entry
     */
    public function moveDraftToOutbox(Conjoon_Modules_Groupware_Email_Draft $draft,
                              Conjoon_Modules_Groupware_Email_Account $account,
                              $userId, $type = '', $referencesId = -1,
                              $postedAttachments, $removeAttachmentIds)
    {

        $emailRecipientsToStringFilter = new Conjoon_Filter_EmailRecipientsToString();
        $emailRecipientsFilter         = new Conjoon_Filter_EmailRecipients();

        $folderModel     = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();
        $outboxModel     = new Conjoon_Modules_Groupware_Email_Item_Model_Outbox();
        $referencesModel = new Conjoon_Modules_Groupware_Email_Item_Model_References();

        $referenceId = $referencesId < 0 ? 0 : $referencesId;

        // prepare data to insert or update
        $outboxUpdate = array(
            'sent_timestamp'              => 0,
            'raw_header'                  => '',
            'raw_body'                    => '',
            'groupware_email_accounts_id' => $account->getId()
        );

        // get the outbox folder.
        $outboxId = $folderModel->getOutboxFolderId($account->getId(), $userId);
        if ($outboxId == 0) {
            return null;
        }

        $date = new Zend_Date($draft->getDate());

        $to           = $draft->getTo();
        $cc           = $draft->getCc();
        $bcc          = $draft->getBcc();
        $fromAddress  = new Conjoon_Modules_Groupware_Email_Address(
            array($account->getAddress(), $account->getUserName())
        );

        $toString     = array();
        foreach ($to as $recipient) {
            $toString[] = $recipient->__toString();
        }
        $toString = implode(', ', $toString);

        $ccString     = array();
        foreach ($cc as $recipient) {
            $ccString[] = $recipient->__toString();
        }
        $ccString = implode(', ', $ccString);

        $bccString = array();
        foreach ($bcc as $recipient) {
            $bccString[] = $recipient->__toString();
        }
        $bccString = implode(', ', $bccString);

        /**
         * @see Conjoon_Filter_DateToUtc
         */
        require_once 'Conjoon/Filter/DateToUtc.php';

        $toUtcFilter = new Conjoon_Filter_DateToUtc();

        $itemUpdate = array(
            'date'                       => $toUtcFilter->filter(
                                                $date->get(Zend_Date::ISO_8601)
                                            ),
            'subject'                    => $draft->getSubject(),
            'from'                       => $fromAddress->__toString(),
            'reply_to'                   => $account->getReplyAddress(),
            'to'                         => $toString,
            'cc'                         => $ccString,
            'bcc'                        => $bccString,
            'sender'                     => $emailRecipientsToStringFilter->filter(
                $emailRecipientsFilter->filter(array(
                    $fromAddress->__toString()
                ))
            ),
            'recipients'                 => $emailRecipientsToStringFilter->filter(
                $emailRecipientsFilter->filter(array(
                    $toString,
                    $ccString,
                    $bccString
                ))
            ),
            'references'                 => $draft->getReferences(),
            'in_reply_to'                => $draft->getInReplyTo(),
            'content_text_html'          => $draft->getContentTextHtml(),
            'content_text_plain'         => $draft->getContentTextPlain(),
            'groupware_email_folders_id' => $outboxId
        );

        $adapter = $this->getAdapter();
        $adapter->beginTransaction();

        $id = $draft->getId();
        try {
            if ($id > 0) {
                // update! move from drafts to outbox

                $itemWhere = $this->getAdapter()->quoteInto('id = ?', $id);
                $this->update($itemUpdate, $itemWhere);

                Conjoon_Util_Array::apply($outboxUpdate, array(
                    'groupware_email_items_id' => $id
                ));
                $outboxWhere = $outboxModel->getAdapter()->quoteInto('groupware_email_items_id = ?', $id);
                $outboxModel->update($outboxUpdate, $outboxWhere);
            } else {

                // insert!
                $id = $this->insert($itemUpdate);

                $draft->setId($id);

                Conjoon_Util_Array::apply($outboxUpdate, array(
                    'groupware_email_items_id' => $id
                ));
                $outboxModel->insert($outboxUpdate);
                $flagUpdate = array(
                    'groupware_email_items_id' => $id,
                    'user_id'                  => $userId,
                    'is_read'                  => 1,
                    'is_spam'                  => 0,
                    'is_deleted'               => 0
                );

                $flagModel = new Conjoon_Modules_Groupware_Email_Item_Model_Flag();
                $flagModel->insert($flagUpdate);
            }

            $this->saveAttachmentsForDraft(
                $draft, $postedAttachments, $removeAttachmentIds
            );

            switch ($type) {
                case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY:
                case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY_ALL:
                case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_FORWARD:
                    if ($referenceId != 0) {
                        $referencesUpdate = array(
                            'groupware_email_items_id' => $id,
                            'user_id'                  => $userId,
                            'reference_items_id'       => $referenceId,
                            'reference_type'           => $type,
                            'is_pending'               => 1
                        );

                        $referencesModel->insert($referencesUpdate);
                    }
                break;
            }

            $adapter->commit();
        } catch (Exception $e) {

            /**
             * @see Conjoon_Log
             */
            require_once 'Conjoon/Log.php';

            Conjoon_Log::log($e, Zend_Log::ERROR);

            $adapter->rollBack();
            return null;
        }

        return $this->getItemForUser($id, $userId);
    }


    /**
     * Saves a sent email into the database.
     *
     * @param Conjoon_Modules_Groupware_Email_Draft $message
     * @param Conjoon_Modules_Groupware_Email_Account $account
     * @param integer $userId
     * @param Conjoon_Mail_Sent $mailSent
     * @param string $type
     * @param integer $referencesId The id of the email that was refernced sending this
     * message. This argument will only be taken into account if $type euqals to
     * reply or reply_all
     * @param array $postedAttachments
     * @param array $removeAttachmentIds
     *
     * @return array the data from groupware_email_item associated with
     * the newly saved entry
     */
    public function saveSentEmail(Conjoon_Modules_Groupware_Email_Draft $message,
                                  Conjoon_Modules_Groupware_Email_Account $account,
                                  $userId, Conjoon_Mail_Sent $mailSent, $type = "",
                                  $referencesId = -1, $postedAttachments = array(),
                                  $removeAttachmentIds = array())
    {
        $mail = $mailSent->getMailObject();

        $userId = (int)$userId;

        $accountId = (int)$account->getId();

        $messageId = (int)$message->getId();

        $referenceId = $referencesId <= 0 ? 0 : $referencesId;

        if ($userId <= 0 || $accountId <= 0) {
            return array();
        }

        $referencesModel = new Conjoon_Modules_Groupware_Email_Item_Model_References();

        $emailRecipientsFilter         = new Conjoon_Filter_EmailRecipients();
        $emailRecipientsToStringFilter = new Conjoon_Filter_EmailRecipientsToString();

        $outboxModel = new Conjoon_Modules_Groupware_Email_Item_Model_Outbox();
        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();

        // first check the folder type of the email
        $folderId = $message->getGroupwareEmailFoldersId();

        $messageType = 'scratch';

        // negative/0, means the message was created from draft
        if ($folderId <= 0) {
            $messageType = 'scratch';
        } else {
            // anything else needs the meta info type fetched out of the folder model
            $metaInfo = $folderModel->getMetaInfo($folderId);
            switch ($metaInfo) {
                case Conjoon_Modules_Groupware_Email_Folder_Model_Folder::META_INFO_OUTBOX:
                    $messageType = 'outbox';
                break;
                case Conjoon_Modules_Groupware_Email_Folder_Model_Folder::META_INFO_DRAFT:
                    $messageType = 'draft';
                break;
                // anything else is probably a reply or forward to an existing message in any other
                // folder
                default:
                    $messageType = 'scratch';
                break;

            }
        }

        // adjust the message type depending on the type
        if ($type == 'reply' || $type == 'reply_all' || $type == 'forward') {
            $messageType = 'scratch';
        }


        // prefill update/insert arrays
        $sentFolderId = $folderModel->getSentFolder($accountId, $userId);
        $date         = new Zend_Date($mail->getDate(), Zend_Date::RFC_2822);
        $replyTo      = (string)$mail->getReplyTo();
        $to           = $message->getTo();
        $cc           = $message->getCc();
        $bcc          = $message->getBcc();
        $fromAddress  = new Conjoon_Modules_Groupware_Email_Address(
            array($account->getAddress(), $account->getUserName())
        );

        $toString     = array();
        foreach ($to as $recipient) {
            $toString[] = $recipient->__toString();
        }
        $toString = implode(', ', $toString);

        $ccString     = array();
        foreach ($cc as $recipient) {
            $ccString[] = $recipient->__toString();
        }
        $ccString = implode(', ', $ccString);

        $bccString = array();
        foreach ($bcc as $recipient) {
            $bccString[] = $recipient->__toString();
        }
        $bccString = implode(', ', $bccString);

        $outboxUpdate = array(
            'sent_timestamp'              => time(),
            'raw_header'                  => $mailSent->getSentHeaderText(),
            'raw_body'                    => $mailSent->getSentBodyText(),
            'groupware_email_accounts_id' => $message->getGroupwareEmailAccountsId()
        );

        /**
         * @see Conjoon_Filter_DateToUtc
         */
        require_once 'Conjoon/Filter/DateToUtc.php';

        $filterToUtc = new Conjoon_Filter_DateToUtc();

        $itemUpdate = array(
            'reply_to'                   => $replyTo,
            'from'                       => $fromAddress->__toString(),
            'recipients'                 => $emailRecipientsToStringFilter->filter(
                $emailRecipientsFilter->filter(array(
                    $toString,
                    $ccString,
                    $bccString
                ))
            ),
            'sender'                 => $emailRecipientsToStringFilter->filter(
                $emailRecipientsFilter->filter(array(
                    $fromAddress->__toString()
                ))
            ),
            'groupware_email_folders_id' => $sentFolderId,
            'date'                       => $filterToUtc->filter(
                                                $date->get(Zend_Date::ISO_8601)
                                            )
        );


        switch ($messageType) {
            // if the message was sent from an opened draft or from the outbox,
            // we simply can create a new entry in the tables,
            // as if it was created from scratch
            // if, however, the email was sent from drafts, a user might have updated
            // the addresses, the subject, the email text and the attachments.
            // those fields have to be updated in the datastorage as well
            case 'draft':

                Conjoon_Util_Array::apply($itemUpdate, array(
                    'subject'            => $message->getSubject(),
                    'to'                 => $toString,
                    'cc'                 => $ccString,
                    'bcc'                => $bccString,
                    'content_text_plain' => $message->getContentTextPlain(),
                    'content_text_html'  => $message->getContentTextHtml(),
                ));

                $this->saveAttachmentsForDraft($message, $postedAttachments, $removeAttachmentIds);

            // most simple: mesageType is outbox which means we have simply to update a few fields
            case 'outbox':
                if ($messageId <= 0 || $sentFolderId == 0) {
                    return array();
                }

                // the message might have referenced an item when it was created.
                // look up entry in reference table and set this to is_pending = false
                $referencesWhere = $referencesModel->getAdapter()->quoteInto(
                                       'groupware_email_items_id = ?',
                                       $messageId
                                   ) . ' AND '
                                   . $referencesModel->getAdapter()->quoteInto(
                                         'user_id = ?',
                                         $userId
                                   );
                $referencesModel->update(array(
                    'is_pending' => 0
                ), $referencesWhere);

                $outboxWhere = $outboxModel->getAdapter()->quoteInto('groupware_email_items_id = ?', $messageId);
                $outboxModel->update($outboxUpdate, $outboxWhere);

                $itemWhere = $this->getAdapter()->quoteInto('id = ?', $messageId);
                $this->update($itemUpdate, $itemWhere);



                return $this->getItemForUser($messageId, $userId);
            break;

            // if the message was created from scratch, i.e. has no id and no folderId,
            // save a fresh row into the tables groupware_email_items_id, groupware_email_items_flags,
            // groupware_email_items_outbox
            case 'scratch':

                    Conjoon_Util_Array::apply($itemUpdate, array(
                        'subject'            => $message->getSubject(),
                        'to'                 => $toString,
                        'cc'                 => $ccString,
                        'bcc'                => $bccString,
                        'in_reply_to'        => $message->getInReplyTo(),
                        'references'         => $message->getReferences(),
                        'content_text_plain' => $message->getContentTextPlain(),
                        'content_text_html'  => $message->getContentTextHtml(),
                    ));

                    $messageId = (int)$this->insert($itemUpdate);

                    if ($messageId <= 0) {
                        return array();
                    }

                    $flagModel = new Conjoon_Modules_Groupware_Email_Item_Model_Flag();

                    $referenceType = '';

                    if (($type == Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY
                        || $type == Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY_ALL
                        || $type == Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_FORWARD)  && $referenceId != 0) {

                        $referenceType = $type;

                        $referenceUpdate = array(
                            'groupware_email_items_id' => $messageId,
                            'user_id'                  => $userId,
                            'reference_items_id'       => $referenceId,
                            'reference_type'           => $referenceType
                        );

                        $referencesModel->insert($referenceUpdate);
                    }

                    $flagUpdate = array(
                        'groupware_email_items_id' => $messageId,
                        'user_id'                  => $userId,
                        'is_read'                  => 1,
                        'is_spam'                  => 0,
                        'is_deleted'               => 0
                    );

                    $flagModel->insert($flagUpdate);

                    Conjoon_Util_Array::apply($outboxUpdate, array(
                        'groupware_email_items_id' => $messageId
                    ));

                    $outboxModel->insert($outboxUpdate);

                    $message->setId($messageId);
                    $this->saveAttachmentsForDraft(
                        $message, $postedAttachments, $removeAttachmentIds
                    );

                    return $this->getItemForUser($messageId, $userId);
            break;
        }

        return null;

    }

    /**
     * Will save attachments for a draft.
     * When requested to save/send a message by a client, all existing
     * attachments/files will be available in the postedAttachments property,
     * data according to the structure of com.conjoon.cudgets.data.FileRecord.
     * Important keys are 'orgId', 'metaType', 'key', 'name'
     *
     * Warning! since its not guaranteed that the ids in removeAttachments are
     * ids for attachments that indeed belong to the draft, it's needed
     * to check whether the current list of attachments holds this id.
     *
     * @param Conjoon_Modules_Groupware_Email_Draft $message
     * @param array $postedAttachments
     * @param array $removeAttachmentIds
     * @param array $attachmentMap
     *
     * @throws InvalidArgumentException
     */
    public function saveAttachmentsForDraft(
        Conjoon_Modules_Groupware_Email_Draft $draft,
        $postedAttachments = array(), $removeAttachmentIds = array(), &$attachmentMap = array())
    {

        if ($draft->getId() <= 0) {
            throw new InvalidArgumentException(
                "Invalid draft supplied - id was ".$draft->getId()
            );
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment
         */
        require_once 'Conjoon/Modules/Groupware/Email/Attachment/Model/Attachment.php';

        $attachmentModel = new Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment();

        // first off, get all the attachments from the draft
        $draftAttachments = $draft->getAttachments();

        $postedEmailAttachmentIds   = array();
        $existingEmailAttachmentIds = array();
        $postedFilesIds             = array();

        $finalPostedFiles         = array();
        $finalPostedAttachments   = array();
        $finalExistingAttachments = array();

        //get ids for emailAttachments
        for ($i = 0, $len = count($postedAttachments); $i < $len; $i++) {
            if ($postedAttachments[$i]['metaType'] == 'emailAttachment') {
                $postedEmailAttachmentIds[] = $postedAttachments[$i]['orgId'];
                $finalPostedAttachments[$postedAttachments[$i]['orgId']] =
                    $postedAttachments[$i];
            } else {
                $postedFilesIds[] = $postedAttachments[$i]['orgId'];
                $finalPostedFiles[$postedAttachments[$i]['orgId']] =
                    $postedAttachments[$i];
            }
        }
        for ($i = 0, $len = count($draftAttachments); $i < $len; $i++) {

            // intersect will be created later
            $existingEmailAttachmentIds[] = $draftAttachments[$i]->getId();

            if (in_array($draftAttachments[$i]->getId(), $removeAttachmentIds)) {
                continue;
            }


            $finalExistingAttachments[$draftAttachments[$i]->getId()] =
                $draftAttachments[$i];
        }

        // finally create the intersection of all ids that are in the
        // lists of items to remove and in the list of existing items
        $removeAttachmentIds = array_values(array_intersect($removeAttachmentIds,
            $existingEmailAttachmentIds
        ));

        // get the ids from the attachments that need to get changed
        $changeNameIds = array_values(array_intersect(
            $postedEmailAttachmentIds, $existingEmailAttachmentIds
        ));

        // get the ids from the attachments that need to get saved, i.e.
        // when a draft was created with email attachments which currently
        // beong to another email
        $copyAttachmentIds = array_values(array_diff(
            $postedEmailAttachmentIds, $existingEmailAttachmentIds
        ));

        // take care of copying attachments
        for ($i = 0, $len = count($copyAttachmentIds); $i < $len; $i++) {
            $id = $copyAttachmentIds[$i];
            $newAttachmentId = $attachmentModel->copyAttachmentForNewItemId(
                $id, $draft->getId(), $finalPostedAttachments[$id]['name']
            );
            if ($newAttachmentId > 0) {
                $attachmentMap[$finalPostedAttachments[$id]['id']] = $newAttachmentId;
            }
        }

        // take care of deleting attachments
        for ($i = 0, $len = count($removeAttachmentIds); $i < $len; $i++) {
            $attachmentModel->deleteAttachmentForId($removeAttachmentIds[$i]);
        }

        // take care of renaming attachments
        for ($i = 0, $len = count($changeNameIds); $i < $len; $i++) {
            $id = $changeNameIds[$i];

            if ($finalExistingAttachments[$id]->getFileName()
                != $finalPostedAttachments[$id]['name']) {
                $updated = $attachmentModel->updateNameForAttachment(
                    $id, $finalPostedAttachments[$id]['name']
                );

                if ($updated) {
                    $finalExistingAttachments[$id]->setFileName(
                        $finalPostedAttachments[$id]['name']
                    );
                    $attachmentMap[$finalPostedAttachments[$id]['id']] = $id;
                }
            }
        }

        // copy files to attachments
        /**
         * @see Conjoon_Modules_Groupware_Files_File_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Files/File/Facade.php';

        $filesFacade = Conjoon_Modules_Groupware_Files_File_Facade::getInstance();

        foreach ($finalPostedFiles as $id => $file) {

            // possible that the file is stored in the file system, so get the content here
            // and pass as argument
            $fileData = $filesFacade->getLobContentWithData(array(
                'id'  => $file['orgId'],
                'key' => $file['key'],
                'includeResource' => true
            ));

            $newAttachmentId = $attachmentModel->copyFromFilesForItemId(
                $file['key'], $file['orgId'], $draft->getId(), $file['name'],
                $fileData['resource']
            );

            if ($newAttachmentId > 0) {
                $attachmentMap[$finalPostedFiles[$id]['id']] =
                $newAttachmentId;
            }
        }

    }

// -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Groupware_Email_Item';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getReferencedItem',
            'getEmailItemsFor',
            'getItemForUser',
            'getItemsForUser',
            'moveDraftToOutbox',
            'saveSentEmail'
        );
    }

}
