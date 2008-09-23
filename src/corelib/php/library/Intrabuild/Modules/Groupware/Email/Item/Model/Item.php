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
                      'recipients',
                      'subject',
                      'sender',
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
            require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Outbox.php';
            require_once 'Intrabuild/Modules/Groupware/Email/Attachment/Model/Attachment.php';

            $inboxModel      = new Intrabuild_Modules_Groupware_Email_Item_Model_Inbox();
            $outboxModel     = new Intrabuild_Modules_Groupware_Email_Item_Model_Outbox();
            $attachmentModel = new Intrabuild_Modules_Groupware_Email_Attachment_Model_Attachment();

            $flagModel->delete('user_id = '.$userId.' AND groupware_email_items_id IN ('.$idString.')');
            $attachmentModel->delete('groupware_email_items_id IN ('.$idString.')');
            $inboxModel->delete('groupware_email_items_id IN ('.$idString.')');
            $outboxModel->delete('groupware_email_items_id IN ('.$idString.')');
        }

        return $deleted;
    }


    /**
     * Saves a sent email into the database.
     *
     * @param Intrabuild_Modules_Groupware_Email_Draft $message
     * @param Intrabuild_Modules_Groupware_Email_Account $account
     * @param integer $userId
     * @param Intrabuild_Mail_Sent $mailSent
     *
     * @return array the data from groupware_email_item associated with
     * the newly saved entry
     */
    public function saveSentEmail(Intrabuild_Modules_Groupware_Email_Draft $message,
                                  Intrabuild_Modules_Groupware_Email_Account $account,
                                  $userId, Intrabuild_Mail_Sent $mailSent)
    {
        $mail = $mailSent->getMailObject();

        $userId = (int)$userId;

        $accountId = (int)$account->getId();

        $messageId = (int)$message->getId();

        if ($userId <= 0 || $accountId <= 0) {
            return array();
        }

        /**
         * @see Intrabuild_Filter_EmailRecipients
         */
        require_once 'Intrabuild/Filter/EmailRecipients.php';

        /**
         * @see Intrabuild_Filter_EmailRecipientsToString
         */
        require_once 'Intrabuild/Filter/EmailRecipientsToString.php';

        /**
         * @see Zend_Date
         */
        require_once 'Zend/Date.php';

        /**
         * @see Intrabuild_Modules_Groupware_Email_Folder_Model_Folder
         */
        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Model/Folder.php';

        /**
         * @see Intrabuild_Modules_Groupware_Email_Item_Model_Outbox
         */
        require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Outbox.php';

        /**
         * @see Intrabuild_Modules_Groupware_Email_Address
         */
        require_once 'Intrabuild/Modules/Groupware/Email/Address.php';

        $emailRecipientsFilter         = new Intrabuild_Filter_EmailRecipients();
        $emailRecipientsToStringFilter = new Intrabuild_Filter_EmailRecipientsToString();

        $outboxModel = new Intrabuild_Modules_Groupware_Email_Item_Model_Outbox();
        $folderModel = new Intrabuild_Modules_Groupware_Email_Folder_Model_Folder();

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
                case Intrabuild_Modules_Groupware_Email_Folder_Model_Folder::META_INFO_OUTBOX:
                    $messageType = 'outbox';
                break;
                case Intrabuild_Modules_Groupware_Email_Folder_Model_Folder::META_INFO_DRAFT:
                    $messageType = 'draft';
                break;
                // anything else is probably a reply or forward to an existing message in any other
                // folder
                default:
                    $messageType = 'scratch';
                break;

            }
        }

        // prefill update/insert arrays
        $sentFolderId = $folderModel->getSentFolder($accountId, $userId);
        $date         = new Zend_Date($mail->getDate(), Zend_Date::RFC_2822);
        $replyTo      = (string)$mail->getReplyTo();
        $to           = $message->getTo();
        $cc           = $message->getCc();
        $bcc          = $message->getBcc();
        $fromAddress  = new Intrabuild_Modules_Groupware_Email_Address(
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
            // most simple: mesageType is outbox which means we have simply to update a few fields
            case 'outbox':
                if ($messageId <= 0 || $sentFolderId == 0) {
                    return array();
                }

                $outboxWhere = $outboxModel->getAdapter()->quoteInto('groupware_email_items_id = ?', $messageId);
                $outboxModel->update($outboxUpdate, $where);

                $itemWhere = $this->getAdapter()->quoteInto('id = ?', $messageId);
                $this->update($itemUpdate, $itemWhere);

                return $this->getItemForUser($messageId, $userId);
            break;

            // if the message was sent from an opened draft, we simply can create a new entry in the tables,
            // as if it was created from scratch
            case 'draft':
            // if the message was created from scratch, i.e. has no id and no folderId,
            // save a fresh row into the tables groupware_email_items_id, groupware_email_items_flags,
            // groupware_email_items_outbox
            case 'scratch':
                    /**
                     * @see Intrabuild_Util_Array
                     */
                    require_once 'Intrabuild/Util/Array.php';

                    Intrabuild_Util_Array::apply($itemUpdate, array(
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

                    /**
                     * @see Intrabuild_Modules_Groupware_Email_Item_Model_Flag
                     */
                    require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Flag.php';

                    $flagModel = new Intrabuild_Modules_Groupware_Email_Item_Model_Flag();

                    $flagUpdate = array(
                        'groupware_email_items_id' => $messageId,
                        'user_id'                  => $userId,
                        'is_read'                  => 1,
                        'is_spam'                  => 0,
                        'is_deleted'               => 0
                    );

                    $flagModel->insert($flagUpdate);

                    Intrabuild_Util_Array::apply($outboxUpdate, array(
                        'groupware_email_items_id' => $messageId
                    ));

                    $outboxModel->insert($outboxUpdate);

                    return $this->getItemForUser($messageId, $userId);
            break;
        }

        return null;

    }



// -------- interface Intrabuild_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Intrabuild_Modules_Groupware_Email_Item';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getEmailItemsFor',
            'saveSentEmail'
        );
    }

}