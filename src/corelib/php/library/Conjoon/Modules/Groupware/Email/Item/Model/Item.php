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
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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
            return $rows;
        }

        return array();
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

        return $row;
    }

    /**
     * Returns the base query for email-items
     *
     * For some queries it can be crucial to add tables which get joined at the beginning
     * of the statement. The optional argument $addTable allows for adding INNER JOIN sources
     * to the beginning of the statement. It keys are "name" which holds a value as specified
     * in the "from" method from Zend_Db_Select, and "cols".
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

            $referencesModel->delete('is_pending=1 AND user_id = '.$userId.' AND groupware_email_items_id IN ('.$idString.')');
            $flagModel->delete('user_id = '.$userId.' AND groupware_email_items_id IN ('.$idString.')');
            $attachmentModel->delete('groupware_email_items_id IN ('.$idString.')');
            $inboxModel->delete('groupware_email_items_id IN ('.$idString.')');
            $outboxModel->delete('groupware_email_items_id IN ('.$idString.')');
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
     *
     * @return array the data from groupware_email_item associated with
     * the newly saved entry
     */
    public function saveDraft(Conjoon_Modules_Groupware_Email_Draft $draft,
                              Conjoon_Modules_Groupware_Email_Account $account,
                              $userId, $type = '', $referencesId = -1)
    {
        $emailRecipientsToStringFilter = new Conjoon_Filter_EmailRecipientsToString();
        $emailRecipientsFilter         = new Conjoon_Filter_EmailRecipients();

        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();
        $outboxModel = new Conjoon_Modules_Groupware_Email_Item_Model_Outbox();

        // prepare data to insert or update
        $outboxUpdate = array(
            'sent_timestamp'              => '',
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

        $itemUpdate = array(
            'date'                       => $date->get(Zend_Date::ISO_8601),
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

                $outboxWhere = $outboxModel->getAdapter()->quoteInto('groupware_email_items_id = ?', $id);
                $outboxModel->update($outboxUpdate, $outboxWhere);
            } else {
                // insert!
                $id = $this->insert($itemUpdate);
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
        } catch (Exception $e) {
            $adapter->rollBack();
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
     *
     * @return array the data from groupware_email_item associated with
     * the newly saved entry
     */
    public function moveDraftToOutbox(Conjoon_Modules_Groupware_Email_Draft $draft,
                              Conjoon_Modules_Groupware_Email_Account $account,
                              $userId, $type = '', $referencesId = -1)
    {

        $emailRecipientsToStringFilter = new Conjoon_Filter_EmailRecipientsToString();
        $emailRecipientsFilter         = new Conjoon_Filter_EmailRecipients();

        $folderModel     = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();
        $outboxModel     = new Conjoon_Modules_Groupware_Email_Item_Model_Outbox();
        $referencesModel = new Conjoon_Modules_Groupware_Email_Item_Model_References();

        $referenceId = $referencesId < 0 ? 0 : $referencesId;

        // prepare data to insert or update
        $outboxUpdate = array(
            'sent_timestamp'              => '',
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

        $itemUpdate = array(
            'date'                       => $date->get(Zend_Date::ISO_8601),
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

        } catch (Exception $e) {
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
     *
     * @return array the data from groupware_email_item associated with
     * the newly saved entry
     */
    public function saveSentEmail(Conjoon_Modules_Groupware_Email_Draft $message,
                                  Conjoon_Modules_Groupware_Email_Account $account,
                                  $userId, Conjoon_Mail_Sent $mailSent, $type = "",
                                  $referencesId = -1)
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
            'raw_header'                  => $mailSent->getHeader(),
            'raw_body'                    => $mailSent->getBody(),
            'groupware_email_accounts_id' => $message->getGroupwareEmailAccountsId()
        );
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
            'date'                       => $date->get(Zend_Date::ISO_8601)
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

                    return $this->getItemForUser($messageId, $userId);
            break;
        }

        return null;

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
            'saveSentEmail',
            'saveDraft'
        );
    }

}