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
 * Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Model_Item
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Item.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Model_Inbox
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Inbox.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Model_Outbox
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Outbox.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Account_Model_Account
 */
require_once 'Conjoon/Modules/Groupware/Email/Account/Model/Account.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Keys
 */
require_once 'Conjoon/Modules/Groupware/Email/Keys.php';

/**
 *
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Draft_Model_Draft {

    /**
     * Returns an assoc array with the data of an draft. The returned array
     * has all properties as according to Conjoon_Modules_Groupware_Email_Draft.
     *
     * @param integer $itemId
     * @param integer $userId
     * @param string $context The context used to fetch the draft. Important
     * when dealign with contexts "reply", "reply_all" and "forward".
     * - context "forward": fields "references" and "in_reply_to" will be set
     * to an empty string
     * - context "reply", "reply_all": "in_reply_to" will be set to the message-id
     * of the email, references will be concatenated with the message-id
     *
     * @return array
     */
    public function getDraft($itemId, $userId, $context = '')
    {
        $itemId = (int)$itemId;

        if ($itemId <= 0) {
            return array();
        }

        $itemModel = new Conjoon_Modules_Groupware_Email_Item_Model_Item();

        $row = $itemModel->fetchRow(
            $itemModel->select()->from($itemModel)
                      ->where('id = ?', $itemId)
        );

        if (!$row) {
            return array();
        }

        $draft = array(
            'id'                         => $row->id,
            'date'                       => $row->date,
            'subject'                    => $row->subject,
            'from'                       => $row->from,
            'reply_to'                   => $row->reply_to,
            'to'                         => $row->to,
            'cc'                         => $row->cc,
            'bcc'                        => $row->bcc,
            'in_reply_to'                => $row->in_reply_to,
            'references'                 => $row->references,
            'content_text_plain'         => $row->content_text_plain,
            'content_text_html'          => $row->content_text_html,
            'groupware_email_folders_id' => $row->groupware_email_folders_id,
            'attachments'                => array()
        );

        // clear memory
        unset($row);

        // set in_reply_to, references according to the context

        switch ($context) {
            case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY:
            case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY_ALL:
                $inboxModel = new Conjoon_Modules_Groupware_Email_Item_Model_Inbox();
                $messageId  = $inboxModel->getMessageIdForItem($draft['id']);
                if ($messageId != "") {
                    $draft['in_reply_to'] = $messageId;
                    $draft['references']  = $draft['references'] != ''
                                          ? $draft['references'] . ' ' . $messageId
                                          : $messageId;
                } else {
                    $draft['in_reply_to'] = '';
                    $draft['references']  = '';
                }
            break;

            case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_FORWARD:
                $draft['in_reply_to'] = '';
                $draft['references']  = '';

            case '':
            case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_EDIT:
                /**
                 * @see Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment
                 */
                require_once 'Conjoon/Modules/Groupware/Email/Attachment/Model/Attachment.php';

                $attachmentModel = new Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment();

                $draft['attachments'] = $attachmentModel
                                        ->getAttachmentsForItem($draft['id'])->toArray();

            break;
        }

        // check if the item is available in outbox and get the id of it under which it was
        // created. Otherwise, get the standard account out of the accounts-table
        $outboxModel = new Conjoon_Modules_Groupware_Email_Item_Model_Outbox();

        $accIdRow = $outboxModel->fetchRow(
            $outboxModel->select()->from($outboxModel, array('groupware_email_accounts_id'))
                        ->where('groupware_email_items_id = ? ', $draft['id'])
        );

        $accountModel = new Conjoon_Modules_Groupware_Email_Account_Model_Account();

        if (!$accIdRow) {
            $accId = $accountModel->getStandardAccountIdForUser($userId);
        } else {
            $accId = $accIdRow->groupware_email_accounts_id;

            // check if the account still exists
            $account = $accountModel->getAccount($accId, $userId);
            if (empty($account)) {
                $accId = $accountModel->getStandardAccountIdForUser($userId);
                if ($accId == 0) {
                    return array();
                }
            }

        }

        $draft['groupware_email_accounts_id'] = $accId;

        return $draft;
    }


}