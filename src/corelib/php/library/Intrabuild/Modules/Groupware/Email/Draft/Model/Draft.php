<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Item.php 153 2008-09-20 20:08:17Z T. Suckow $
 * $Date: 2008-09-20 22:08:17 +0200 (Sa, 20 Sep 2008) $
 * $Revision: 153 $
 * $LastChangedDate: 2008-09-20 22:08:17 +0200 (Sa, 20 Sep 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild_rep/trunk/src/corelib/php/library/Intrabuild/Modules/Groupware/Email/Item/Model/Item.php $
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
 * @see Intrabuild_Modules_Groupware_Email_Item_Model_Item
 */
require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Item.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Item_Model_Inbox
 */
require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Inbox.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Item_Model_Outbox
 */
require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Outbox.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Account_Model_Account
 */
require_once 'Intrabuild/Modules/Groupware/Email/Account/Model/Account.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Keys
 */
require_once 'Intrabuild/Modules/Groupware/Email/Keys.php';

/**
 *
 * @package Intrabuild_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Draft_Model_Draft {

    /**
     * Returns an assoc array with the data of an draft. The returned array
     * has all properties as according to Intrabuild_Modules_Groupware_Email_Draft.
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

        $itemModel = new Intrabuild_Modules_Groupware_Email_Item_Model_Item();

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
            'groupware_email_folders_id' => $row->groupware_email_folders_id
        );

        // clear memory
        unset($row);

        // set in_reply_to, references according to the context

        switch ($context) {
            case Intrabuild_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY:
            case Intrabuild_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY_ALL:
                $inboxModel = new Intrabuild_Modules_Groupware_Email_Item_Model_Inbox();
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

            case Intrabuild_Modules_Groupware_Email_Keys::REFERENCE_TYPE_FORWARD:
                $draft['in_reply_to'] = '';
                $draft['references']  = '';
            break;
        }

        // check if the item is available in outbox and get the id of it under which it was
        // created. Otherwise, get the standard account out of the accounts-table
        $outboxModel = new Intrabuild_Modules_Groupware_Email_Item_Model_Outbox();

        $accIdRow = $outboxModel->fetchRow(
            $outboxModel->select()->from($outboxModel, array('groupware_email_accounts_id'))
                        ->where('groupware_email_items_id = ? ', $draft['id'])
        );

        $accountModel = new Intrabuild_Modules_Groupware_Email_Account_Model_Account();

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