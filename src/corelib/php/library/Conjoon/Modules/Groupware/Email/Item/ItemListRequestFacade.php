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
 * This facade eases the access to often neededoperations on requesting
 * email item lists.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Item_ItemListRequestFacade {

    /**
     * @var Conjoon_Modules_Groupware_Email_Item_ItemListRequestFacade
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Groupware_Email_Folder_Facade
     */
    private $_folderFacade = null;

    /**
     * Enforce singleton.
     *
     */
    private function __construct()
    {
    }

    /**
     * Enforce singleton.
     *
     */
    private function __clone()
    {
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Facade
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


// -------- public api

    /**
     * Returns the list of email items for the specified folder path for the
     * specified sort info.
     *
     * @param string $path
     * @param integer $userId
     * @param array $sortInfo An array with the following key/value pairs:
     *  - sort  => name of the header to sort after
     *  - dir   => sort direction - ASC or DESC
     *  - limit => max number of messagesto sort
     *  - start => starting position of message to sort
     *
     * @return array
     *
     * @throws Conjoon_Argument_Exception
     */
    public function getEmailItemList($path, $userId, Array $sortInfo = array())
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        $data = array('path' => $path, 'userId' => $userId);

        Conjoon_Argument_Check::check(array(
            'path' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),
            'userId' => array(
                'type'       => 'int',
                'allowEmpty' => false
            )
        ), $data);

        $path   = $data['path'];
        $userId = $data['userId'];

        /**
         * @see Conjoon_Text_Parser_Mail_MailboxFolderPathParser
         */
        require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathParser.php';

        $parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathParser();

        $pathInfo = $parser->parse($data['path']);


        if ($this->_getFolderFacade()->isRemoteFolder($pathInfo['rootId'])) {

            $accountDto = $this->_getFolderFacade()->getImapAccountForFolderIdAndUserId(
                $pathInfo['rootId'], $userId
            );

            return $this->_getEmailItemListForAccountAndRemoteFolder(
                $accountDto, $pathInfo['path'], $sortInfo, $userId
            );
        }

        throw new RuntimeException("Anything but remote folder nto supported by this facade yet");

    }

// -------- helper

    /**
     *
     * @param Conjoon_Modules_Groupware_Email_Account_Dto $accountDto
     * @param $path
     * @param array $sortInfo
     *
     * @return array
     *
     * @throws Conjoon_Argument_Exception
     */
    protected function _getEmailItemListForAccountAndRemoteFolder(
            Conjoon_Modules_Groupware_Email_Account_Dto $accountDto, $path,
            Array $sortInfo = array(), $userId/*REMOVE THIS ARGUMENT*/)
    {

        $globalName = $this->_getFolderFacade()->getImapGlobalNameForAccountAndPath(
            $accountDto, $path
        );

        /**
         * @see Conjoon_Date_Format
         */
        require_once 'Conjoon/Date/Format.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_ImapHelper
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

        $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
                    ::reuseImapProtocolForAccount($accountDto);

        /**
         * @see Zend_Mail_Storage_Imap
         */
        require_once 'Conjoon/Mail/Storage/Imap.php';

        $storage = new Conjoon_Mail_Storage_Imap($protocol);


        $messageStruct = $storage->getHeaderListAndMetaInformationForGlobalName(
            $globalName
        );

        /**
         * @see Conjoon_Text_Parser_Mail_MessageHeaderParser
         */
        require_once 'Conjoon/Text/Parser/Mail/MessageHeaderParser.php';

        $parser = new Conjoon_Text_Parser_Mail_MessageHeaderParser();

        /**
         * @see Conjoon_Text_Transformer_DateStringSanitizer
         */
        require_once 'Conjoon/Text/Transformer/DateStringSanitizer.php';

        $sanitizeDateTransformer = new Conjoon_Text_Transformer_DateStringSanitizer();

        /**
         * @see Conjoon_Text_Transformer_MimeDecoder
         */
        require_once 'Conjoon/Text/Transformer/MimeDecoder.php';

        $mimeDecoder = new Conjoon_Text_Transformer_MimeDecoder();

        /**
         * @see Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
         */
        require_once 'Conjoon/Text/Parser/Mail/EmailAddressIdentityParser.php';

        $identityParser       = new Conjoon_Text_Parser_Mail_EmailAddressIdentityParser();
        $identityParserSender = new Conjoon_Text_Parser_Mail_EmailAddressIdentityParser(array(
            'addSlashes' => false, 'useQuoting' => false
        ));

        /**
         * @see Conjoon_Text_Transformer_Mail_EmailAddressNameQuoteTransformer
         */
        require_once 'Conjoon/Text/Transformer/Mail/EmailAddressNameQuoteTransformer.php';

        $quoteTransformer = new Conjoon_Text_Transformer_Mail_EmailAddressNameQuoteTransformer();


        $parsedHeaders = array();
        for ($i = 0, $len = count($messageStruct); $i < $len; $i++) {

            $header = $parser->parse($messageStruct[$i]['header']);

            $header['date'] = Conjoon_Date_Format::toUtc(
                $sanitizeDateTransformer->transform($header['date'])
            );

            // no mime decode needed, already done in EmailHeaderParser
            $header['subject'] = $mimeDecoder->transform($header['subject']);

            // RECIPIENTS IS NOT PART OF THE HEADER.
            $recipients = array_merge(
                (isset($header['to']) ? $identityParser->parse($header['to']) : array()),
                (isset($header['cc']) ? $identityParser->parse($header['cc']) : array()),
                (isset($header['bcc']) ? $identityParser->parse($header['bcc']) : array())
            );

            $recipientNames = array();
            foreach ($recipients as $address => $addressValue) {
                if (isset($addressValue[1])) {
                    $recipientNames[] = $quoteTransformer->transform($addressValue[1]);
                } else {
                    $recipientNames[] = $addressValue[0];
                }
            }

            $header['recipients'] = $recipientNames;


            // SENDER IS NOT PART OF THE HEADER.
            $sender = $identityParserSender->parse($header['from']);
            $header['sender'] = isset($sender[0][1]) ? $sender[0][1] : $sender[0][0];

            // the uid
            $header['uid'] = $messageStruct[$i]['uid'];

            $header['isAttachment']     = false;
            $header['isRead']           = false;
            $header['referencedAsType'] = array();

            // flag processing
            $flags =& $messageStruct[$i]['flags'];
            for ($u = 0, $lenu = count($flags); $u < $lenu; $u++) {
                if ($flags[$u] == '\Seen') {
                    $header['isRead'] = true;
                } else if (stripos($flags[$u], 'forwarded') !== false) {
                    array_push($header['referencedAsType'], 'forwarded');
                } else if ($flags[$u] == '\Answered') {
                    array_push($header['referencedAsType'], 'reply');
                }
            }

            // possible attachment?
            $bodystructure =& $messageStruct[$i]['bodystructure'];
            for ($u = 0, $lenu = count($bodystructure); $u < $lenu; $u++) {
                if (isset($bodystructure[$u][5])
                    && strtolower($bodystructure[$u][5]) == 'base64'
                    && isset($bodystructure[$u][8])
                    && is_array($bodystructure[$u][8])
                    && isset($bodystructure[$u][8][0])
                    && strtolower($bodystructure[$u][8][0]) == 'attachment') {
                    $header['isAttachment'] = true;

                    break;
                }

            }

            $parsedHeaders[] = $header;
        }

        // PARSED HEADERS IS THE INPUT AS WE NEED IT FOR STORING INTO THE DB
        // NOW GENERATE THEOUTPUT AS CONJOON EXPECTS IT!

        /**
         * @see Conjoon_Date_Format
         */
        require_once 'Conjoon/Date/Format.php';

        /**
         * @see Zend_Filter_HtmlEntities
         */
        require_once 'Zend/Filter/HtmlEntities.php';

        $htmlEntitiesFilter = new Zend_Filter_HtmlEntities(array(
            'quotestyle' => ENT_COMPAT
        ));


        $responseItems = array();
        for ($i = 0, $len = count($parsedHeaders); $i < $len; $i++) {

             $header =& $parsedHeaders[$i];
             $responseItems[] = array(
                'id'                      => $header['uid'],
                'recipients'              => $header['recipients'],
                'sender'                  => $header['sender'],
                'subject'                 => $htmlEntitiesFilter->filter(
                                                     $header['subject']
                                             ),
                'date'                    => Conjoon_Date_Format::utcToLocal(
                                                 $header['date']
                                             ),
                'isRead'                  => (int)$header['isRead'],
                'isAttachment'            => (int)$header['isAttachment'],
                'isSpam'                  => 0,
                'isDraft'                 => 0,
                'isOutboxPending'         => 0,
                'referencedAsTypes'       => $header['referencedAsType'],
                'groupwareEmailFoldersId' => 1
             );

        }

        // sort
        switch ($sortInfo['sort']) {
            case 'date':
                usort($responseItems, array($this, 'memorySortDate'));
                break;

            case 'subject':
                usort($responseItems, array($this, 'memorySortSubject'));
                break;

            case 'recipients':
                usort($responseItems, array($this, 'memorySortRecipients'));
                break;

            case 'sender':
                usort($responseItems, array($this, 'memorySortSender'));
                break;

            case 'is_spam':
                usort($responseItems, array($this, 'memorySortIsSpam'));
                break;

            case 'is_read':
                usort($responseItems, array($this, 'memorySortIsRead'));
                break;

            case 'is_attachment':
                usort($responseItems, array($this, 'memorySortisAttachment'));
                break;
        }

        if ($sortInfo['dir'] == 'DESC') {
            $responseItems = array_reverse($responseItems);
        }

        return $responseItems;

    }

    public function memorySortSubject($a, $b)
    {
        return strcmp($a['subject'], $b['subject']);
    }

    public function memorySortDate($a, $b)
    {
        $date = date_create($a['date']);
        $a    = date_timestamp_get($date);

        $date = date_create($b['date']);
        $b    = date_timestamp_get($date);

        if ((int)$a == (int)$b) {
            return 0;
        }

        return ((int)$a < (int)$b) ? -1 : 1;
    }

    public function memorySortSender($a, $b)
    {
        return strcmp($a['sender'], $b['sender']);
    }

    public function memorySortRecipients($a, $b)
    {
        if (!isset($a['recipients'][0])) {
            return 0;
        }

        if (!isset($b['recipients'][0])) {
            return 0;
        }

        return strcmp($a['recipients'][0], $b['recipients'][0]);
    }

    public function memorySortIsSpam($a, $b)
    {
        if ((int)$a['isSpam'] == (int)$b['isSpam']) {
            return 0;
        }

        return ((int)$a['isSpam'] < (int)$b['isSpam']) ? -1 : 1;
    }

    public function memorySortIsRead($a, $b)
    {
        if ((int)$a['isRead'] == (int)$b['isRead']) {
            return 0;
        }

        return ((int)$a['isRead'] < (int)$b['isRead']) ? -1 : 1;
    }

    public function memorySortIsAttachment($a, $b)
    {
        if ((int)$a['isAttachment'] == (int)$b['isAttachment']) {
            return 0;
        }

        return ((int)$a['isAttachment'] < (int)$b['isAttachment']) ? -1 : 1;
    }


    /**
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Facade
     */
    private function _getFolderFacade()
    {
        if (!$this->_folderFacade) {
            /**
             * @see Conjoon_Modules_Groupware_Email_Folder_Facade
             */
            require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';

            $this->_folderFacade = Conjoon_Modules_Groupware_Email_Folder_Facade
                                   ::getInstance();
        }

        return $this->_folderFacade;
    }

}