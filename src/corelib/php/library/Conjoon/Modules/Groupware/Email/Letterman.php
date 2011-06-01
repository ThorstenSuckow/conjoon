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
 * @see Zend_Mime
 */
require_once 'Zend/Mime.php';

/**
 * @see Zend_Mime_Decode
 */
require_once 'Zend/Mime/Decode.php';

/**
 * @see Conjoon_BeanContext_Decorator
 */
require_once 'Conjoon/BeanContext/Decorator.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Model_Inbox
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Inbox.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Model_Item
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Item.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Model_Flag
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Flag.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment
 */
require_once 'Conjoon/Modules/Groupware/Email/Attachment/Model/Attachment.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Attachment_Filter_Attachment
 */
require_once 'Conjoon/Modules/Groupware/Email/Attachment/Filter/Attachment.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Filter_Flag
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/Flag.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Filter_Item
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/Item.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Filter_Inbox
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/Inbox.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Account
 */
require_once 'Conjoon/Modules/Groupware/Email/Account.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Account_Model_Account
 */
require_once 'Conjoon/Modules/Groupware/Email/Account/Model/Account.php';

/**
 * @see Conjoon_Mail_Storage_Pop3
 */
require_once 'Conjoon/Mail/Storage/Pop3.php';

/**
 * @see Conjoon_Mail_Storage_Imap
 */
require_once 'Conjoon/Mail/Storage/Imap.php';

/**
 * @see Conjoon_Util_Array
 */
require_once 'Conjoon/Util/Array.php';

/**
 * @see Conjoon_Error
 */
require_once 'Conjoon/Error.php';

/**
 * @see Conjoon_Filter_MimeDecodeHeader
 */
require_once 'Conjoon/Filter/MimeDecodeHeader.php';

/**
 * @see Conjoon_Db_Util
 */
require_once 'Conjoon/Db/Util.php';

/**
 * @see Conjoon_Util_Format
 */
require_once 'Conjoon/Util/Format.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Folder_Model_Folder
 */
require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Attachment_Facade
 */
require_once 'Conjoon/Modules/Groupware/Email/Attachment/Facade.php';

/**
 * A utility class for fetching/sending emails.
 *
 * @category   Email
 * @package    Conjoon_Modules_Groupware
 * @subpackage Conjoon_Modules_Groupware_Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Conjoon_Modules_Groupware_Email_Letterman {

    const ICONV_OLD   = 'old';
    const ICONV_UTF_8 = 'UTF-8';

    private $_filterAttachment = null;
    private $_filterFlag       = null;
    private $_filterItem       = null;
    private $_filterInbox      = null;

    private $_modelAttachment   = null;
    private $_attachmentFacadce = null;
    private $_modelFlag         = null;
    private $_modelItem         = null;
    private $_modelInbox        = null;
    private $_modelFolder       = null;

    private $_lastIconvError = false;

    private $_cachedUidList = array();

    private $_maxAllowedPacket = 0;

    private $_maxMemory = 0;

    /**
     * @var Conjoon_Modules_Groupware_Email_Letterman
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
        $context = Conjoon_Filter_Input::CONTEXT_CREATE;

        $this->_filterAttachment = new Conjoon_Modules_Groupware_Email_Attachment_Filter_Attachment(array(), $context);
        $this->_filterFlag       = new Conjoon_Modules_Groupware_Email_Item_Filter_Flag(array(), $context);
        $this->_filterItem       = new Conjoon_Modules_Groupware_Email_Item_Filter_Item(array(), $context);
        $this->_filterInbox      = new Conjoon_Modules_Groupware_Email_Item_Filter_Inbox(array(), $context);

        $this->_modelAttachment  = new Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment();
        $this->_modelFlag        = new Conjoon_Modules_Groupware_Email_Item_Model_Flag();
        $this->_modelItem        = new Conjoon_Modules_Groupware_Email_Item_Model_Item();
        $this->_modelInbox       = new Conjoon_Modules_Groupware_Email_Item_Model_Inbox();
        $this->_modelFolder      = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();
        $this->_attachmentFacade = Conjoon_Modules_Groupware_Email_Attachment_Facade::getInstance();
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
    public static function fetchEmails($userId, Conjoon_Modules_Groupware_Email_Account $accountId)
    {
        if (!self::$_instance) {
            self::$_instance = new Conjoon_Modules_Groupware_Email_Letterman();
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
     * @param integer $userId
     * @param integer $accountId
     * @oaram array $emailItem
     *
     * @todo implement logic, think about creating a filter from it
     */
    private function _assignFolderId($userId, $accountId, Array &$emailItem)
    {
        $folderId = $this->_modelFolder->getInboxFolderId($accountId, $userId);

        if (!$folderId) {
            throw new Exception(
                "Critical exception: Could not find default inbox folder "
                ."for user $userId"
            );
        }

        $emailItem['groupwareEmailFoldersId'] = $folderId;
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
            Conjoon_Modules_Groupware_Email_Item_Model_Inbox::UID
        );
    }

    /**
     * Strategyfor computing the chunk size for _cacheUidl.
     * Depending on the passed argument, the returned value will be either
     * a larger number to reduce calls to the database table.
     *
     * @param integer $number
     *
     * @return integer
     */
    private function _computeChunkSize($number)
    {
        $number = (int)$number;

        switch (true) {

            case ($number <= 20) :
                return 20;
            break;

            case ($number <= 100) :
                return 100;
            break;

            case ($number <= 500) :
                return 150;
            break;

            case ($number <= 1000) :
                return 200;
            break;
        }

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
     * Conjoon_Modules_Groupware_Email_Model_Inbox::computeMessageHash.
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
                Conjoon_Modules_Groupware_Email_Item_Model_Inbox::MESSAGE_ID
            );
        }

        $hash = Conjoon_Modules_Groupware_Email_Item_Model_Inbox::computeMessageHash($rawHeader, $rawBody);

        return $this->_modelInbox->isUniqueKeyPresent(
            $hash,
            $accountId,
            Conjoon_Modules_Groupware_Email_Item_Model_Inbox::HASH
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
    private static function _splitMessage($message, &$header, &$body, $EOL = Zend_Mime::LINEEND)
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
            $config = Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT);
            $this->_maxAllowedPacket = $config->database->variables->max_allowed_packet;
            if (!$this->_maxAllowedPacket) {
                $this->_maxAllowedPacket = Conjoon_Db_Util::getMaxAllowedPacket($dbAdapter);
            }
        }

        $this->_setPlainFromHtml($emailItem);
        // filter and insert into groupware_email_items
        $filterItem->setData($emailItem);
        $itemData = $filterItem->getProcessedData();

        if ($this->_maxAllowedPacket < strlen($emailItem['rawBody'])) {
            return 'Could not save message with subject "'.$itemData['subject']
                   .'" - message is larger than available packet size ('.$this->_maxAllowedPacket. ' bytes).';
        }

        $dbAdapter->beginTransaction();

        $currFilter = null;

        try {

            Conjoon_Util_Array::underscoreKeys($itemData);
            $id = (int)$modelItem->insert($itemData);

            if ($id <= 0) {
                return null;
            }

            // assign needed (reference) keys
            $emailItem['isRead']                = 0;
            $emailItem['id']                    = $id;
            $emailItem['groupwareEmailItemsId'] = $id;

            // filter and insert into groupware_email_items_inbox
            $currFilter = $filterInbox;
            $filterInbox->setData($emailItem);

            $itemData = $filterInbox->getProcessedData();

            Conjoon_Util_Array::underscoreKeys($itemData);
            $modelInbox->insert($itemData);

            // filter and insert into groupware_email_items_flag
            $currFilter = $filterFlag;
            $filterFlag->setData($emailItem);
            $itemData = $filterFlag->getProcessedData();

            Conjoon_Util_Array::underscoreKeys($itemData);
            $modelFlag->insert($itemData);

            // loop through attachments and insert into groupware_email_items_attachments
            $attachmentCount = count($emailItem['attachments']);
            $currFilter = $filterAttachment;
            for ($i = 0; $i < $attachmentCount; $i++) {
                $emailItem['attachments'][$i]['groupwareEmailItemsId'] = $id;
                $filterAttachment->setData($emailItem['attachments'][$i]);
                $itemData = $filterAttachment->getProcessedData();
                Conjoon_Util_Array::underscoreKeys($itemData);
                $modelAttachment->insert($itemData);
            }

            $dbAdapter->commit();

            return $id;

        } catch (Exception $e) {

            if ($e instanceof Zend_Filter_Exception) {
                $error = Conjoon_Error::fromFilter($currFilter, $e);
                $error = $error->getMessage();
            } else {
                $error = $e->getMessage();
            }
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
    * @param int $accountId The id of the account to fetch the emails for
    *
    * @return Array An associative array with the keys of the fetched and saved
    * emails in the array 'fetched', and error-messages in the key 'errors'.
    */
    private function _fetchEmails($userId, Conjoon_Modules_Groupware_Email_Account $account)
    {
        $fetchedEmailIds    = array();
        $fetchedEmailErrors = array();

        $userId = (int)$userId;

        if ($userId <= 0) {
            return $fetchedEmailIds;
        }

        $transports = array(
            Conjoon_Modules_Groupware_Email_Account::PROTOCOL_POP3 => "Conjoon_Mail_Storage_Pop3",
            Conjoon_Modules_Groupware_Email_Account::PROTOCOL_IMAP => "Conjoon_Mail_Storage_Imap"
        );

        self::_setIconvEncoding(self::ICONV_UTF_8);

        $accountId = $account->getId();
        $transport = $transports[$account->getProtocol()];
        $isPop3    = $account->getProtocol() == Conjoon_Modules_Groupware_Email_Account::PROTOCOL_POP3;

        $isCopyLeftOnServer = $account->isCopyLeftOnServer();

        $mail = new $transport(array(
            'host'     => $account->getServerInbox(),
            'port'     => $account->getPortInbox(),
            'user'     => $account->getUsernameInbox(),
            'password' => $account->getPasswordInbox()
        ));

        $hasUniqueId = $mail->hasUniqueId;

        $mailCount = count($mail);

        if ($hasUniqueId) {
            $uidl = $mail->getUniqueId();
            $this->_cacheUidl($uidl, $accountId, $this->_computeChunkSize($mailCount));

            // this is to prevent undefined indexes when the count of uidl
            // differs from the number of emails fetched. This is a very rare error
            // that occures now and then - see http://wiki.conjoon.org/ticket/189
            // it's assumed its related to connection aborts during communication
            // with the mail server
            if (count($uidl) != $mailCount) {
                return array(
                    'fetched' => $fetchedEmailIds,
                    'errors'  => array(
                        'Could not retrieve messages - number of items in unique id list ' .
                        'differs from total number of emails on the server: ' .
                        'Number of unique ids: '.count($uidl).'; number of messages: '.$mailCount .'; ' .
                        'This is possibly related to a connection abort while attempting to fetch ' .
                        'messages from a server. Please try again.'
                    )
                );
            }
        }

        $messagesToRemove = array();

        $startMail = max(1, $mailCount - 10);

        for ($oo = $startMail; $oo < $mailCount+1; $oo++) {
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
                        $messagesToRemove[] = $messageNum;
                    }
                    continue;
                } else {
                    $emailItem['uid'] = $uidl[$oo];
                }
            }

            // check here if we can process the message, taking memory limit
            // of php ini into account
            if (!$this->_maxMemory) {
                $this->_maxMemory = Conjoon_Util_Format::convertToBytes(ini_get('memory_limit'));
            }
            $s = $mail->getSize($messageNum);

            if ($s == 0) {
                $fetchedEmailErrors[] = 'Could not save message No. '
                                        .$messageNum
                                        .' - message size seems to be 0 bytes';
                continue;
            }

            if ($this->_maxMemory / $s <= 17) {
                $fetchedEmailErrors[] = 'Could not save message No. '
                                        .$messageNum
                                        .' - message could exceed available memory size ('
                                        .$this->_maxMemory
                                        . ' bytes, message size '.$s.').';
                continue;
            }

            self::_splitMessage($mail->getRawMessage($messageNum), $rawHeader, $rawBody);

            $message = new Conjoon_Mail_Message(array(
                'headers'    => $rawHeader,
                'noToplines' => true,
                'content'    => $rawBody
            ));

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
                        $messagesToRemove[] = $messageNum;
                    }
                    continue;
                }
            } else {
                if (!$isCopyLeftOnServer && $isPop3) {
                    $messagesToRemove[] = $messageNum;
                }
            }

            $mail->noop();

            $emailItem['attachments'] = array();
            $emailItem['userId']      = $userId;

            try {
                $emailItem['from'] = $message->from;
            } catch (Zend_Mail_Exception $e) {
                // may be changed to localized header values by anti vir programs
                try  {
                    $emailItem['from'] = $message->von;
                } catch (Zend_Mail_Exception $e) {
                    $emailItem['from'] = "-";
                }
            }

            if (!isset($emailItem['from'])) {
                throw new Zend_Mail_Exception("No header with the name \"from\" found. Please check if you have an anti virus program runnning in the background. Some are known to change the header values to localized derivates.");
            }

            $emailItem['subject'] = "";

            // very few emails will come in without a subject.
            try {
                $emailItem['subject'] = $message->subject;
            } catch (Zend_Mail_Exception $e) {
                try {
                    // may be changed to localized header values by anti vir programs
                    $emailItem['subject'] = $message->betreff;
                } catch (Zend_Mail_exception $e) {
                    // ignore
                }
            } catch (Zend_Mail_exception $e) {
                // ignore
            }

            $emailItem['date'] = "";

            // date field will be given presedence
            try {
                $emailItem['date'] = $message->date;
            } catch (Zend_Mail_Exception $e) {
                // ignore
            }

            // if date not found, look up deliveryDate
            if (!$emailItem['date']) {
                try {
                    $emailItem['date'] = $message->deliveryDate;
                } catch (Zend_Mail_Exception $e) {
                    // ignore
                }

                if (!$emailItem['date']) {
                    try {
                        // may be changed to localized header values by anti vir programs
                        $emailItem['date'] = $message->datum;
                    } catch (Zend_Mail_Exception $e) {
                        // ignore
                    }
                }
            }

            try {
                $emailItem['to'] = $message->to;
            } catch (Zend_Mail_Exception $e) {
                // "to" might not be used, instead "cc" will be probably available
                // then
                $emailItem['to'] = "";
            }

            if (!$emailItem['to']) {
                try {
                    // may be changed to localized header values by anti vir programs
                    $emailItem['to'] = $message->an;
                } catch (Zend_Mail_Exception $e) {
                    // ignore
                }
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
            $this->_assignFolderId($userId, $accountId, $emailItem);

            $emailItem['rawHeader'] =& $rawHeader;
            $emailItem['rawBody']   =& $rawBody;

            $mail->noop();

            if (!$emailItem['messageId']) {
                $emailItem['hash'] = Conjoon_Modules_Groupware_Email_Item_Model_Inbox::computeMessageHash(
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
                $messagesToRemove[] = $messageNum;
            }
        }

        $messagesToRemove = array_unique($messagesToRemove);
        foreach ($messagesToRemove as $id) {
            $mail->removeMessage($id);
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
            $html = str_replace(array('<br>', '<br />', '<br/>', "<BR>", "<BR/>", "<BR />"), "\r\n", $html);
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

                case 'multipart/related':
                    $this->_parseMultipartRelated($part, $emailItem);
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
                // this is a fallback for "end is missing", if a mime message does not contain
                // the closing boundary
                $ct       = $message->getContent();
                $boundary = $message->getHeaderField('content-type', 'boundary');
                if ($boundary) {
                    $p = strpos($ct, '--' . $boundary . '--');
                    if ($p === false) {
                        $ct .= "\r\n" . '--' . $boundary . '--';
                        $message = new Conjoon_Mail_Message(array(
                            'headers'    => 'Content-Type: '
                                            . $message->contentType,
                            'noTopLines' => true,
                            'content'    => $ct
                        ));

                        $len = 2;
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
                $nm = new Conjoon_Mail_Message(array('raw' => $part->getContent()));
                $n = $nm->subject;
                $filter = new Conjoon_Filter_MimeDecodeHeader();
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
            'contentId' => $contentId,
            'key'       => $this->_attachmentFacade->generateAttachmentKey($emailItem['userId'])
        );

    }

}