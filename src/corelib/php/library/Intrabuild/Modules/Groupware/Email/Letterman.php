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
 * @see Zend_Mime
 */
require_once 'Zend/Mime.php';

/**
 * @see Zend_Mime_Decode
 */
require_once 'Zend/Mime/Decode.php';

/**
 * @see Intrabuild_BeanContext_Decorator
 */
require_once 'Intrabuild/BeanContext/Decorator.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Item_Model_Inbox
 */
require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Inbox.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Item_Model_Item
 */
require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Item.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Item_Model_Flag
 */
require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Flag.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Attachment_Model_Attachment
 */
require_once 'Intrabuild/Modules/Groupware/Email/Attachment/Model/Attachment.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Attachment_Filter_Attachment
 */
require_once 'Intrabuild/Modules/Groupware/Email/Attachment/Filter/Attachment.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Item_Filter_Flag
 */
require_once 'Intrabuild/Modules/Groupware/Email/Item/Filter/Flag.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Item_Filter_Item
 */
require_once 'Intrabuild/Modules/Groupware/Email/Item/Filter/Item.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Item_Filter_Inbox
 */
require_once 'Intrabuild/Modules/Groupware/Email/Item/Filter/Inbox.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Account
 */
require_once 'Intrabuild/Modules/Groupware/Email/Account.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Account_Model_Account
 */
require_once 'Intrabuild/Modules/Groupware/Email/Account/Model/Account.php';

/**
 * @see Intrabuild_Mail_Storage_Pop3
 */
require_once 'Intrabuild/Mail/Storage/Pop3.php';

/**
 * @see Intrabuild_Mail_Storage_Imap
 */
require_once 'Intrabuild/Mail/Storage/Imap.php';

/**
 * @see Intrabuild_Util_Array
 */
require_once 'Intrabuild/Util/Array.php';

/**
 * @see Intrabuild_Error
 */
require_once 'Intrabuild/Error.php';

/**
 * @see Intrabuild_Filter_MimeDecodeHeader
 */
require_once 'Intrabuild/Filter/MimeDecodeHeader.php';

/**
 * @see Intrabuild_Db_Util
 */
require_once 'Intrabuild/Db/Util.php';

/**
 * A utility class for fetching/sending emails.
 *
 * @category   Email
 * @package    Intrabuild_Modules_Groupware
 * @subpackage Intrabuild_Modules_Groupware_Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Intrabuild_Modules_Groupware_Email_Letterman {

    const ICONV_OLD   = 'old';
    const ICONV_UTF_8 = 'UTF-8';

    private $_filterAttachment = null;
    private $_filterFlag       = null;
    private $_filterItem       = null;
    private $_filterInbox      = null;

    private $_modelAttachment = null;
    private $_modelFlag       = null;
    private $_modelItem       = null;
    private $_modelInbox      = null;
    private $_modelAccount    = null;

    private $_lastIconvError = false;

    private $_cachedUidList = array();

    private $_accountModelDecorator = null;

    private $_maxAllowedPacket = 0;

    /**
     * @var Intrabuild_Modules_Groupware_Email_Letterman
     */
    private static $_instance;

    /**
     * @var integer
     */
    private $_attachmentCounter = 1;

    /**
     * @var array
     */
    private static $_oldEncodings = array();

    /**
     * Constructor.
     * Private to enforce singleton behavior.
     *
     *
     */
    private function __construct()
    {
        $context = Intrabuild_Filter_Input::CONTEXT_CREATE;

        $this->_filterAttachment = new Intrabuild_Modules_Groupware_Email_Attachment_Filter_Attachment(array(), $context);
        $this->_filterFlag       = new Intrabuild_Modules_Groupware_Email_Item_Filter_Flag(array(), $context);
        $this->_filterItem       = new Intrabuild_Modules_Groupware_Email_Item_Filter_Item(array(), $context);
        $this->_filterInbox      = new Intrabuild_Modules_Groupware_Email_Item_Filter_Inbox(array(), $context);

        $this->_modelAttachment = new Intrabuild_Modules_Groupware_Email_Attachment_Model_Attachment();
        $this->_modelFlag       = new Intrabuild_Modules_Groupware_Email_Item_Model_Flag();
        $this->_modelItem       = new Intrabuild_Modules_Groupware_Email_Item_Model_Item();
        $this->_modelInbox      = new Intrabuild_Modules_Groupware_Email_Item_Model_Inbox();
        $this->_modelAccount    = new Intrabuild_Modules_Groupware_Email_Account_Model_Account();

        $this->_accountModelDecorator = new Intrabuild_BeanContext_Decorator($this->_modelAccount);
    }

    /**
     * Looks up the singleton instance for this class and returns the
     * return value from _fetchEmails
     *
     * @param int $userId The id of the user to process the email-accounts for.
     * @param int $accountId The id of the account to fetch the emails for, or null
     * to query all accounts
     *
     * @return array
     */
    public static function fetchEmails($userId, $accountId = null)
    {
        if (!self::$_instance) {
            self::$_instance = new Intrabuild_Modules_Groupware_Email_Letterman();
        }

        return self::$_instance->_fetchEmails($userId, $accountId);
    }


    /**
     * Sets the iconv-internal-encodings, since Zend_Mime does not allow
     * for passing an indivdual charset for decoding.
     * This is a simple helper which allows for either setting the encoding
     * to utf-8 or reset the endoding to the old value.
     *
     * @param string $type
     *
     */
    private static function _setIconvEncoding($type)
    {
        if ($type != self::ICONV_UTF_8) {
            if (!empty(self::$_oldEncodings)) {
                iconv_set_encoding('input_encoding',    self::$_oldEncodings['input_encoding']);
                iconv_set_encoding('output_encoding',   self::$_oldEncodings['output_encoding']);
                iconv_set_encoding('internal_encoding', self::$_oldEncodings['internal_encoding']);
            }
        } else {
            if(empty(self::$_oldEncodings)) {
                self::$_oldEncodings = array(
                    'input_encoding'    => iconv_get_encoding('input_encoding'),
                    'output_encoding'   => iconv_get_encoding('output_encoding'),
                    'internal_encoding' => iconv_get_encoding('internal_encoding')
                );
            }

            iconv_set_encoding('input_encoding', 'UTF-8');
            iconv_set_encoding('output_encoding', 'UTF-8');
            iconv_set_encoding('internal_encoding', 'UTF-8');
        }
    }

    /**
     * Checks wether the email should be marked as spam.
     *
     * @todo implement logic, think about creating filter from it
     */
    private function _assignJunkStatus($userId, Array &$emailItem)
    {
        $emailItem['isSpam'] = 0;
    }

    /**
     * Assigns a folder id to the fetched email.
     *
     * @todo implement logic, think about creating a filter from it
     */
    private function _assignFolderId($userId, Array &$emailItem)
    {
        $emailItem['groupwareEmailFoldersId'] = 2;
    }

    /**
     * Checks wether a message with a uid is already present in the db.
     *
     * @param integer $accountId The id of the account for hich the check has to be made.
     * @param string $uid The server-generated uid to look up
     *
     * @return boolean true, if a message with the specified uid is already stored in the db
     * for this account, otherwise false
     */
    private function _isUidPresent($accountId, $uid)
    {
        $cached =& $this->_cachedUidList;

        if (isset($cached[$accountId])) {
            if (isset($cached[$accountId][$uid])) {
                return true;
            } else {
                return false;
            }
        }

        return $this->_modelInbox->isUniqueKeyPresent(
            $uid,
            $accountId,
            Intrabuild_Modules_Groupware_Email_Item_Model_Inbox::UID
        );
    }

    /**
     * Caches the uid list as returned by the server, by querying chunks of values
     * against the db to look up already stored uids.
     * The uid-list is an assoc array with the key being the message number, and
     * the value being the uid of this message, as generated by the server:
     * ( [integer] => [string] )
     *
     * @param array $uidList The array containing the uids
     * @param integer $accountId The id of the account to cache the uid-list for
     * @param integer $chunks The number of uids to send to the server in one query,
     * defaults to 20
     */
    private function _cacheUidl(Array $uidList, $accountId, $chunks = 20)
    {
        $chunks = (int)$chunks;
        if ($chunks <= 0) {
            return;
        }

        $values = array_values($uidList);
        if (count($values) == 0) {
            return;
        }

        $this->_cachedUidList[$accountId] = array();
        $cachedUidList =& $this->_cachedUidList[$accountId];
        while (count($uidList) > 0) {
            $arrayChunks = array_splice($uidList, 0, $chunks);

            $rows = $this->_modelInbox->getMatchingUids(
                $arrayChunks,
                $accountId
            );

            for ($i = 0, $len = count($rows); $i < $len; $i++) {
                $cachedUidList[$rows[$i]['uid']] = true;
            }
        }

    }

    /**
     * Checks wether a message with the specified message-id is already present in the db
     * for this account.
     * If the supplied argument for message_id is empty, the method will try to
     * check for uniqueness of this message based on the computed hash, as returned by
     * Intrabuild_Modules_Groupware_Email_Model_Inbox::computeMessageHash.
     *
     * @param string $messageId The message id to look up. Might be empty.
     * @param integer $accountId The account id for which this message-id should be lokked up.
     * @param string $rawHeader The unmodified, raw header of this message as returned
     * by the server
     * @param string $rawBody The unmodfied, raw body of this message as returned by the server,
     * including attachments et all.
     *
     * @return boolean true, if a message with the specified message-id/hash is already stored
     * in the db for this account, otherwise false
     */
    private function _isMessageIdPresent($messageId, $accountId, &$rawHeader, &$rawBody)
    {
        if (trim((string)$messageId) != "") {
            return $this->_modelInbox->isUniqueKeyPresent(
                $messageId,
                $accountId,
                Intrabuild_Modules_Groupware_Email_Item_Model_Inbox::MESSAGE_ID
            );
        }

        $hash = Intrabuild_Modules_Groupware_Email_Item_Model_Inbox::computeMessageHash($rawHeader, $rawBody);

        return $this->_modelInbox->isUniqueKeyPresent(
            $hash,
            $accountId,
            Intrabuild_Modules_Groupware_Email_Item_Model_Inbox::HASH
        );
    }

    /**
     * Splits a message into its header and its body, writing the results
     * into $hader and $body
     *
     * @param string $message The message to split
     * @param string $header The var to store the header into
     * @param string $body The var to store the body into
     * @param  string $EOL EOL string; defaults to {@link Zend_Mime::LINEEND}
     *
     */
    private static function _splitMessage(&$message, &$header, &$body, $EOL = Zend_Mime::LINEEND)
    {
        // code taken from ZF Zend_Mime_Decode::splitMessage V1.5.2
        // find an empty line between headers and body
        // default is set new line
        if (strpos($message, $EOL . $EOL)) {
            list($header, $body) = explode($EOL . $EOL, $message, 2);
        // next is the standard new line
        } else if ($EOL != "\r\n" && strpos($message, "\r\n\r\n")) {
            list($header, $body) = explode("\r\n\r\n", $message, 2);
        // next is the other "standard" new line
        } else if ($EOL != "\n" && strpos($message, "\n\n")) {
            list($header, $body) = explode("\n\n", $message, 2);
        // at last resort find anything that looks like a new line
        } else {
            @list($header, $body) = @preg_split("%([\r\n]+)\\1%U", $message, 2);
        }
    }

    /**
     * Saves the email and it's attachments. Uses the default db adapter as
     * configured by the application. If an exception occurs, the exception's
     * message will be stored in an array (together with other exceptions that
     * may have occured) and returned later on. Any db operation that failed will
     * be rolled back.
     *
     * @param array $emailItem An associative array with the data to insert into the
     * different tables. All attachments will be stored in the key/value pair "attachments",
     * which is itself a numeric array
     *
     * @return mixed Return the id of the last inserted email item, or an
     * error message if an error occured.
     */
    private function _saveEmail(Array $emailItem)
    {
        $filterAttachment = $this->_filterAttachment;
        $filterFlag       = $this->_filterFlag;
        $filterItem       = $this->_filterItem;
        $filterInbox      = $this->_filterInbox;

        $modelAttachment = $this->_modelAttachment;
        $modelFlag       = $this->_modelFlag;
        $modelItem       = $this->_modelItem;
        $modelInbox      = $this->_modelInbox;

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();

        if (!$this->_maxAllowedPacket) {
            $config = Zend_Registry::get(Intrabuild_Keys::REGISTRY_CONFIG_OBJECT);
            $this->_maxAllowedPacket = $config->database->variables->max_allowed_packet;
            if (!$this->_maxAllowedPacket) {
                $this->_maxAllowedPacket = Intrabuild_Db_Util::getMaxAllowedPacket($dbAdapter);
            }
        }

        $this->_setPlainFromHtml($emailItem);
        // filter and insert into groupware_email_items
        $filterItem->setData($emailItem);
        $itemData = $filterItem->getProcessedData();

        if ($this->_maxAllowedPacket < strlen($emailItem['rawBody'])) {
            return 'Could not save message with subject "'.$itemData['subject']
                   .'" - message is larger than allowed size ('.$this->_maxAllowedPacket. ' bytes).';
        }

        $dbAdapter->beginTransaction();

        try {

            Intrabuild_Util_Array::underscoreKeys($itemData);
            $id = (int)$modelItem->insert($itemData);

            if ($id <= 0) {
                return null;
            }

            // assign needed (reference) keys
            $emailItem['isRead']                = 0;
            $emailItem['id']                    = $id;
            $emailItem['groupwareEmailItemsId'] = $id;

            // filter and insert into groupware_email_items_inbox
            $filterInbox->setData($emailItem);

            $itemData = $filterInbox->getProcessedData();

            Intrabuild_Util_Array::underscoreKeys($itemData);
            $modelInbox->insert($itemData);

            // filter and insert into groupware_email_items_flag
            $filterFlag->setData($emailItem);
            $itemData = $filterFlag->getProcessedData();

            Intrabuild_Util_Array::underscoreKeys($itemData);
            $modelFlag->insert($itemData);

            // loop through attachments and insert into groupware_email_items_attachments
            $attachmentCount = count($emailItem['attachments']);
            for ($i = 0; $i < $attachmentCount; $i++) {
                $emailItem['attachments'][$i]['groupwareEmailItemsId'] = $id;
                $filterAttachment->setData($emailItem['attachments'][$i]);
                $itemData = $filterAttachment->getProcessedData();
                Intrabuild_Util_Array::underscoreKeys($itemData);
                $modelAttachment->insert($itemData);
            }

            $dbAdapter->commit();

            return $id;

        } catch (Exception $e) {
            $error = $e->getMessage();
            try {
                $dbAdapter->rollBack();
            } catch (Exception $m) {
                $error .= '; '.$m->getMessage();
            }

            return $error;
        }
    }


    /**
    * @param int $userId The id of the user to process the email-accounts for.
    * @param int $accountId The id of the account to fetch the emails for, or null
    * to query all accounts
    *
    * @return Array An associative array with the keys of the fetched and saved
    * emails in the array 'fetched', and error-messages in the key 'errors'.
    */
    private function _fetchEmails($userId, $accountId = null)
    {
        $fetchedEmailIds    = array();
        $fetchedEmailErrors = array();

        $userId = (int)$userId;

        if ($userId <= 0) {
            return $fetchedEmailIds;
        }

        if ($accountId !== null) {
            $accounts = array($this->_accountModelDecorator->getAccountAsEntity($accountId, $userId));
        } else {
            $accounts = $this->_accountModelDecorator->getAccountsForUserAsEntity($userId);
        }
        $account = null;
        $transports = array(
            Intrabuild_Modules_Groupware_Email_Account::PROTOCOL_POP3 => "Intrabuild_Mail_Storage_Pop3",
            Intrabuild_Modules_Groupware_Email_Account::PROTOCOL_IMAP => "Intrabuild_Mail_Storage_Imap"
        );

        self::_setIconvEncoding(self::ICONV_UTF_8);
        for ($i = 0, $len = count($accounts); $i < $len; $i++) {
            $account        = $accounts[$i];
            $accountId      = $account->getId();
            $transport      = $transports[$account->getProtocol()];
            $isPop3         = $account->getProtocol() == Intrabuild_Modules_Groupware_Email_Account::PROTOCOL_POP3;

            $isCopyLeftOnServer = $account->isCopyLeftOnServer();

            $mail = new $transport(array(
                'host'     => $account->getServerInbox(),
                'port'     => $account->getPortInbox(),
                'user'     => $account->getUsernameInbox(),
                'password' => $account->getPasswordInbox()
            ));

            $hasUniqueId = $mail->hasUniqueId;

            $mailCount = count($mail);

            if ($mail->hasUniqueId) {
                $uidl = $mail->getUniqueId();
                $this->_cacheUidl($uidl, $accountId);
                // compute message list based on messages already in the db
            }

            for ($oo = 1; $oo < $mailCount+1; $oo++) {
                $messageNum = $oo;
                $this->_attachmentCounter = 1;
                $emailItem = array();
                $rawHeader = "";
                $rawBody   = "";

                // check if the account supports UIDL, and skip the message
                // if it is already available in the db
                if ($hasUniqueId) {
                    if ($this->_isUidPresent($accountId, $uidl[$oo]) === true) {
                        if (!$isCopyLeftOnServer && $isPop3) {
                            $mail->removeMessage($messageNum);
                        }
                        continue;
                    } else {
                        $emailItem['uid'] = $uidl[$oo];
                    }
                }

                $rm      = $mail->getRawMessage($messageNum);
                $message = new Zend_Mail_Message(array(
                    'raw' => $rm
                ));

                self::_splitMessage($rm, $rawHeader, $rawBody);

                $messageId = "";
                try {
                    $messageId = $message->messageId;
                } catch (Zend_Mail_Exception $e) {
                    // ignore
                }

                $emailItem['messageId'] = $messageId;

                $mail->noop();

                // check here if we can remove the mail from the server
                // check first if UIDL is supported. if not, look up the
                // message
                if (!$hasUniqueId) {
                    $id = $this->_isMessageIdPresent($messageId, $accountId, $rawHeader, $rawBody);
                    $mail->noop();

                    if ($id === true) {
                        if (!$isCopyLeftOnServer && $isPop3) {
                            $mail->removeMessage($messageNum);
                        }
                        continue;
                    }
                } else {
                    if (!$isCopyLeftOnServer && $isPop3) {
                        $mail->removeMessage($messageNum);
                    }
                }

                $mail->noop();

                $emailItem['attachments'] = array();
                $emailItem['userId']      = $userId;
                $emailItem['from']        = $message->from;

                // very few emails will come in without a subject.
                try {
                    $emailItem['subject'] = $message->subject;
                } catch (Zend_Mail_exception $e) {
                    $emailItem['subject'] = "";
                }

                $emailItem['date'] = "";

                // date field will be given presedence
                try {
                    $emailItem['date'] = $message->date;
                    if (!$emailItem['date']) {
                        $emailItem['date'] = $message->deliveryDate;
                    }
                } catch (Zend_Mail_Exception $e) {
                    // ignore
                }


                try {
                    $emailItem['to'] = $message->to;
                } catch (Zend_Mail_Exception $e) {
                    // "to" might not be used, instead "cc" will be probably available
                    // then
                    $emailItem['to'] = "";
                }

                try {
                    $emailItem['cc'] = $message->cc;
                } catch (Zend_Mail_Exception $e) {
                    $emailItem['cc'] = '';
                }

                try {
                    $emailItem['references'] = $message->references;
                } catch (Zend_Mail_Exception $e) {
                    $emailItem['references'] = '';
                }

                try {
                    $emailItem['replyTo'] = $message->replyTo;
                } catch (Zend_Mail_Exception $e) {
                    $emailItem['replyTo'] = '';
                }

                try {
                    $emailItem['inReplyTo'] = $message->inReplyTo;
                } catch (Zend_Mail_Exception $e) {
                    $emailItem['inReplyTo'] = '';
                }

                $encodingInformation = $this->_getEncodingInformation($message);

                $contentType = $encodingInformation['contentType'];

                $mail->noop();
                switch ($contentType) {
                    case 'text/plain':
                        $emailItem['contentTextPlain'] = $this->_decode($message->getContent(), $encodingInformation);
                    break;

                    case 'text/html':
                        $emailItem['contentTextHtml'] = $this->_decode($message->getContent(), $encodingInformation);
                    break;

                    case 'multipart/mixed':
                        $this->_parseMultipartMixed($message, $emailItem);
                    break;

                    case 'multipart/alternative':
                        $this->_parseMultipartAlternative($message, $emailItem);
                    break;

                    case 'multipart/related':
                        $this->_parseMultipartRelated($message, $emailItem);
                    break;

                    case 'multipart/signed':
                        $this->_parseMultipartSigned($message, $emailItem);
                    break;

                    case 'multipart/report':
                        $this->_parseMultipartReport($message, $emailItem);
                    break;

                    default:
                        $emailItem['contentTextPlain'] = $this->_decode($message->getContent(), $encodingInformation);
                    break;
                }

                $mail->noop();

                if (!isset($emailItem['contentTextPlain'])) {
                    $emailItem['contentTextPlain'] = '';
                }

                if (!isset($emailItem['contentTextHtml'])) {
                    $emailItem['contentTextHtml'] = '';
                }



                $this->_assignJunkStatus($userId, $emailItem);
                $this->_assignFolderId($userId, $emailItem);

                $emailItem['rawHeader'] =& $rawHeader;
                $emailItem['rawBody']   =& $rawBody;

                $mail->noop();

                if (!$emailItem['messageId']) {
                    $emailItem['hash'] = Intrabuild_Modules_Groupware_Email_Item_Model_Inbox::computeMessageHash(
                        $rawHeader,
                        $rawBody
                    );
                }
                $mail->noop();
                $saved = $this->_saveEmail($emailItem, $userId);
                $mail->noop();
                if (is_int($saved) > 0) {
                    $fetchedEmailIds[] = $saved;
                } else {
                    $fetchedEmailErrors[] = $saved;
                    continue;
                }

                $mail->noop();

                if (!$isCopyLeftOnServer && $isPop3) {
                   $mail->removeMessage($messageNum);
                }
            }
        }
        self::_setIconvEncoding(self::ICONV_OLD);

        return array(
            'fetched' => $fetchedEmailIds,
            'errors'  => $fetchedEmailErrors
        );
      }


// -------- parser helper

    private function _setPlainFromHtml(&$emailItem)
    {
        if ($emailItem['contentTextHtml'] != "" && $emailItem['contentTextPlain'] === "") {
            $html = $emailItem['contentTextHtml'];
            $html = str_replace(array('<br>', '<br />', '<br/>'), "\r\n", $html);
            $html = str_replace('&nbsp;', ' ', $html);
            $html = strip_tags($html);
            $html = htmlspecialchars_decode($html);
            $html = html_entity_decode($html, ENT_COMPAT, 'UTF-8');
            $emailItem['contentTextPlain'] = trim($html);
        }
    }

    private function _decode($text, Array $encodingInformation)
    {
        $charset                 = $encodingInformation['charset'];
        $contentTransferEncoding = $encodingInformation['contentTransferEncoding'];

        switch ($contentTransferEncoding) {
            case 'base64':
                $text = base64_decode($text);
            break;
            case 'quoted-printable':
                $text = quoted_printable_decode($text);
            break;
        }

        // try to replace those curved quotes with their correct entities!
        // see http://en.wikipedia.org/wiki/Quotation_mark_glyphs
        // [quote]
        // A few mail clients send curved quotes using the windows-1252 codes,
        // but mark the text as ISO-8859-1, causing problems for decoders that
        // do not make the dubious assumption that C1 control codes in ISO-8859-1
        // text were meant to be windows-1252 printable characters
        // [/quote]
        if (strtolower($charset) == 'iso-8859-1') {
            $charset = 'windows-1252';
        }

        $this->_setIconvErrorHandler();
        if ($charset != "") {
            $conv = iconv($charset, 'UTF-8', $text);

            // first off, check if the charset is windows-1250 if  encoding fails
            // broaden to windows-1252 then
            if (($conv === false || $this->_lastIconvError) && strtolower($charset) == 'windows-1250') {
                $this->_lastIconvError = false;
                $conv = iconv('windows-1252', 'UTF-8', $text);
            }

            // check if the charset is us-ascii and broaden to windows-1252
            // if encoding attempt fails
            if (($conv === false || $this->_lastIconvError) && strtolower($charset) == 'us-ascii') {
                $this->_lastIconvError = false;
                $conv = iconv('windows-1252', 'UTF-8', $text);
            }

            // fallback! if we have mb-extension installed, we'll try to detect the encoding, if
            // first try with iconv didn't work
            if (($conv === false || $this->_lastIconvError) && function_exists('mb_detect_encoding')) {
                $this->_lastIconvError = false;
                $peekEncoding = mb_detect_encoding($text, $this->_getEncodingList(), true);
                $conv = iconv($peekEncoding, 'UTF-8', $text);
            }
            if ($conv === false || $this->_lastIconvError) {
                $this->_lastIconvError = false;
                $conv = iconv($charset, 'UTF-8//TRANSLIT', $text);
            }
            if ($conv === false || $this->_lastIconvError) {
                $this->_lastIconvError = false;
                $conv = iconv($charset, 'UTF-8//IGNORE', $text);
            }
            if ($conv !== false && !$this->_lastIconvError) {
                $text = $conv;
            }

        } else {
            $conv = false;
            if (function_exists('mb_detect_encoding')) {
                $this->_lastIconvError = false;
                $peekEncoding = mb_detect_encoding($text, $this->_getEncodingList(), true);
                $conv = iconv($peekEncoding, 'UTF-8', $text);
            }
            if ($conv === false || $this->_lastIconvError) {
                $this->_lastIconvError = false;
                $conv = iconv('UTF-8', 'UTF-8//IGNORE', $text);
            }
            if ($conv !== false && !$this->_lastIconvError) {
                $text = $conv;
            }
        }
        $this->_restoreErrorHandler();

        return $text;
    }

    private function _getEncodingList()
    {
        return 'UCS-4, UCS-4BE, UCS-4LE, UCS-2, UCS-2BE, UCS-2LE, UTF-32, UTF-32BE, UTF-32LE, UTF-16, UTF-16BE, UTF-16LE, UTF-8, UTF-7, UTF7-IMAP,  ASCII, EUC-JP, SJIS, eucJP-win, CP51932, JIS, ISO-2022-JP,  ISO-2022-JP-MS, Windows-1252, ISO-8859-1, ISO-8859-2, ISO-8859-3, ISO-8859-4,  ISO-8859-5, ISO-8859-6, ISO-8859-7, ISO-8859-8, ISO-8859-9, ISO-8859-10, ISO-8859-13,  ISO-8859-14, ISO-8859-15, ISO-8859-16, EUC-CN, CP936, HZ, EUC-TW, BIG-5, EUC-KR,  UHC, ISO-2022-KR, Windows-1251, CP866, KOI8-R, ArmSCII-8';
    }

    private function _setIconvErrorHandler()
    {
        $this->_lastIconvError = false;
        set_error_handler(array($this, '_iconvErrorHandler'));
    }

    private function _restoreErrorHandler()
    {
        $this->_lastIconvError = false;
        restore_error_handler();
    }


    private function _iconvErrorHandler()
    {
        $this->_lastIconvError = true;
    }

    private function _getEncodingInformation($message)
    {
        $contentTransferEncoding = "";
        $charset                 = "";
        $contentType             = "";
        // also look up name for attachments
        $name = "";

        try {
            $contentTransferEncoding = $message->contentTransferEncoding;
        } catch (Zend_Mail_Exception $e) {
                //
        }

        try {
            $contentType = $message->contentType;
            if (strpos($contentType, ';') !== false) {
                $contentType = strtok($message->contentType, ';');

                while (($value = strtok(';'))!== false) {
                    $value = trim($value);
                    if (strpos($value, 'charset') === 0) {
                        $charset = trim($value);
                    } else if (strpos($value, 'name') === 0) {
                        $name = trim($value);
                    }
                }

                if ($charset != "") {
                    // probably no ";" used as separator, but line-feed or space.
                    if (strpos($charset, "\r\n") !== false || strpos($charset, "\n") !== false
                    || strpos($charset, "\r") !== false || strpos($charset, " ") !== false) {
                        $sep = "__IB_".time()."_EOL__";
                        $charset = str_replace(array(" ","\r\n", "\n", "\r"), $sep, $charset);
                        $charsets = explode($sep, $charset);
                        $charset = $charsets[0];
                    }
                    $charset = str_replace(array('charset=', '"', "'"), '' , $charset);
                }
                if ($name != "") {
                    $name = str_replace(array('name=', '"', "'"), '' , $name);
                }
            }

        } catch (Zend_Mail_Exception $e) {
            // ignore
        }

        return array(
            'contentType'             => strtolower($contentType),
            'charset'                 => strtolower($charset),
            'name'                    => $name,
            'contentTransferEncoding' => strtolower($contentTransferEncoding)
        );
    }



    /**
     *
     */
    private function _parseMultipartMixed($message, &$emailItem)
    {
        $len = $message->countParts()+1;

        for ($i = 1; $i < $len; $i++) {
            $part = $message->getPart($i);

            $encodingInformation = $this->_getEncodingInformation($part);
            $contentType         = $encodingInformation['contentType'];

            // skip to attachments if encodingInformation detects "name" value
            if (isset($encodingInformation['name']) && $encodingInformation['name'] != "") {
                $contentType = "___";
            }

            switch ($contentType) {
                case 'text/plain':
                    if (!isset($emailItem['contentTextPlain'])) {
                        $emailItem['contentTextPlain'] = $this->_decode($part->getContent(), $encodingInformation);
                    }
                break;

                case 'text/html':
                    if (!isset($emailItem['contentTextHtml'])) {
                        $emailItem['contentTextHtml'] = $this->_decode($part->getContent(), $encodingInformation);
                    }
                break;

                case 'multipart/alternative':
                    $this->_parseMultipartAlternative($part, $emailItem);
                break;

                default:
                    $this->_parseAttachments($part, $emailItem);
                break;
            }
        }
    }

    /**
     *
     */
    private function _parseMultipartAlternative($message, &$emailItem)
    {
        try {
            $len = $message->countParts()+1;
        } catch (Zend_Exception $e) {
            /**
             * @todo Zed_Mime_decode does not throw a Zen_Mime_Exception, but a
             * Zend_Exception if the boundaries end is missing.
             * submit a bug report for this
             */
             try {
                $ct       = $message->getContent();
                $boundary = $message->getHeaderField('content-type', 'boundary');
                if ($boundary) {
                    $p = strpos($ct, '--' . $boundary . '--');
                    if ($p === false) {
                        $ct .= "\r\n" . '--' . $boundary . '--';
                        $hd = $message->getHeaders();
                        $headers = array();
                        foreach ($hd as $key => $value) {
                            $headers[] = $key . ': '.$value;
                        }
                        $message = new Zend_Mail_Message(array('raw' => implode("\r\n", $headers) . "\r\n" . $ct));
                        $len     = 2;
                    }
                } else {
                    throw new Zend_Mail_Exception('');
                }
            } catch (Zend_Mail_Exception $e) {
                $encodingInformation = $this->_getEncodingInformation($message);
                $contentType         = $encodingInformation['contentType'];
                if ($contentType == 'text/plain') {
                    $emailItem['contentTextPlain'] = $this->_decode($ct, $encodingInformation);
                } else if ($contentType == 'text/html') {
                    $emailItem['contentTextHtml'] = $this->_decode($ct, $encodingInformation);
                }
                return;
            }
        }

        for ($i = 1; $i < $len; $i++) {
            $part = $message->getPart($i);

            $encodingInformation = $this->_getEncodingInformation($part);
            $contentType         = $encodingInformation['contentType'];

            switch ($contentType) {
                case 'text/plain':
                    if (!isset($emailItem['contentTextPlain'])) {
                        $emailItem['contentTextPlain'] = $this->_decode($part->getContent(), $encodingInformation);
                    }
                break;

                case 'text/html':
                    if (!isset($emailItem['contentTextHtml'])) {
                        $emailItem['contentTextHtml'] = $this->_decode($part->getContent(), $encodingInformation);
                    }
                break;

                case 'multipart/related':
                    $this->_parseMultipartRelated($part, $emailItem);
                break;
            }
        }
    }

    /**
     *
     */
    private function _parseMultipartSigned($message, &$emailItem)
    {
        $len = $message->countParts()+1;

        for ($i = 1; $i < $len; $i++) {
            $part = $message->getPart($i);

            $encodingInformation = $this->_getEncodingInformation($part);
            $contentType         = $encodingInformation['contentType'];

            switch ($contentType) {
                case 'text/plain':
                    if (!isset($emailItem['contentTextPlain'])) {
                        $emailItem['contentTextPlain'] = $this->_decode($part->getContent(), $encodingInformation);
                    }
                break;

                case 'text/html':
                    if (!isset($emailItem['contentTextHtml'])) {
                        $emailItem['contentTextHtml'] = $this->_decode($part->getContent(), $encodingInformation);
                    }
                break;

                case 'multipart/alternative':
                    $this->_parseMultipartAlternative($part, $emailItem);
                break;

                default:
                    $this->_parseAttachments($part, $emailItem);
                break;
            }
        }
    }

    /**
     *
     */
    private function _parseMultipartReport($message, &$emailItem)
    {
        $len = $message->countParts()+1;

        $defCharsetForDeliveryStatus = null;
        for ($i = 1; $i < $len; $i++) {
            $part = $message->getPart($i);

            $encodingInformation = $this->_getEncodingInformation($part);
            $contentType         = $encodingInformation['contentType'];

            switch ($contentType) {
                case 'text/plain':
                    if (!isset($emailItem['contentTextPlain'])) {
                        $defCharsetForDeliveryStatus = $encodingInformation['charset'];
                        $emailItem['contentTextPlain'] = $this->_decode($part->getContent(), $encodingInformation);
                    }
                break;

                case 'text/html':
                    if (!isset($emailItem['contentTextHtml'])) {
                        $emailItem['contentTextHtml'] = $this->_decode($part->getContent(), $encodingInformation);
                    }
                break;

                default:
                    $this->_parseAttachments($part, $emailItem);
                break;
            }
        }
    }

    /**
     *
     */
    private function _parseMultipartRelated($message, &$emailItem)
    {
        $len = $message->countParts()+1;

        for ($i = 1; $i < $len; $i++) {
            $part = $message->getPart($i);

            $encodingInformation = $this->_getEncodingInformation($part);
            $contentType         = $encodingInformation['contentType'];

            switch ($contentType) {
                case 'text/plain':
                    if (!isset($emailItem['contentTextPlain'])) {
                        $emailItem['contentTextPlain'] = $this->_decode($part->getContent(), $encodingInformation);
                    }
                break;

                case 'text/html':
                    if (!isset($emailItem['contentTextHtml'])) {
                        $emailItem['contentTextHtml'] = $this->_decode($part->getContent(), $encodingInformation);
                    }
                break;

                case 'multipart/alternative':
                    $this->_parseMultipartAlternative($part, $emailItem);
                break;

                default:
                    $this->_parseAttachments($part, $emailItem);
                break;
            }
        }
    }

    /**
     *
     */
    private function _parseAttachments($part, &$emailItem)
    {
        $encodingInformation = $this->_getEncodingInformation($part);

        $fileName                = $encodingInformation['name'];
        $contentType             = $encodingInformation['contentType'];
        $contentTransferEncoding = $encodingInformation['contentTransferEncoding'];

        if ($contentType == 'message/rfc822' || $contentType == 'rfc822') {
            try {
                $nm = new Zend_Mail_Message(array('raw' => $part->getContent()));
                $n = $nm->subject;
                $filter = new Intrabuild_Filter_MimeDecodeHeader();
                $fileName = $filter->filter($n).'.eml';
            } catch (Zend_Mail_Exception $e) {
                // ignore
            }
        }

        if ($fileName === "") {
            $fileName = 'attachment['.($this->_attachmentCounter++).']';
        }

        try {
            $contentId = $part->contentId;
        } catch (Zend_Mail_Exception $e) {
            $contentId = "";
        }

        $emailItem['attachments'][] = array(
            'mimeType'  => $contentType,
            'encoding'  => $contentTransferEncoding,
            'content'   => $part->getContent(),
            'fileName'  => $fileName,
            'contentId' => $contentId
        );

    }

}