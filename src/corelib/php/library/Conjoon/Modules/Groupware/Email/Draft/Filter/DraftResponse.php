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
 * @see Conjoon_Filter_Input
 */
require_once 'Conjoon/Filter/Input.php';

/**
 * @see Conjoon_Filter_Raw
 */
require_once 'Conjoon/Filter/Raw.php';

/**
 * @see Zend_Filter_HtmlEntities
 */
require_once 'Zend/Filter/HtmlEntities.php';

/**
 * @see Conjoon_Filter_StringPrependIf
 */
require_once 'Conjoon/Filter/StringPrependIf.php';

/**
 * @see Conjoon_Filter_StringWrap
 */
require_once 'Conjoon/Filter/StringWrap.php';

/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating feed items.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Draft_Filter_DraftResponse extends Conjoon_Filter_Input {

    const CONTEXT_REPLY     = 'reply';
    const CONTEXT_REPLY_ALL = 'reply_all';
    const CONTEXT_FORWARD   = 'forward';
    const CONTEXT_EDIT      = 'edit';
    const CONTEXT_NEW       = 'new';


    protected $_presence = array(
        self::CONTEXT_NEW => array(
            'name',
            'address',
            'subject',
            'contentTextPlain',
            'groupwareEmailAccountsId',
            'groupwareEmailFoldersId',
            'id'
        ),

        self::CONTEXT_EDIT => array(
            'id',
            'date',
            'subject',
            'from',
            'replyTo',
            'to',
            'cc',
            'bcc',
            'inReplyTo',
            'references',
            'contentTextPlain',
            'contentTextHtml',
            'groupwareEmailFoldersId',
            'groupwareEmailAccountsId',
            'userEmailAddresses'
        ),

        self::CONTEXT_FORWARD => array(
            'id',
            'date',
            'subject',
            'from',
            'replyTo',
            'to',
            'cc',
            'bcc',
            'inReplyTo',
            'references',
            'contentTextPlain',
            'contentTextHtml',
            'groupwareEmailFoldersId',
            'groupwareEmailAccountsId'
        ),

        self::CONTEXT_REPLY_ALL => array(
            'id',
            'date',
            'subject',
            'from',
            'replyTo',
            'to',
            'cc',
            'userEmailAddresses',
            'bcc',
            'inReplyTo',
            'references',
            'contentTextPlain',
            'contentTextHtml',
            'groupwareEmailFoldersId',
            'groupwareEmailAccountsId',
            'userEmailAddresses'
        ),

        self::CONTEXT_REPLY => array(
            'id',
            'date',
            'subject',
            'from',
            'replyTo',
            'to',
            'cc',
            'bcc',
            'inReplyTo',
            'references',
            'contentTextPlain',
            'contentTextHtml',
            'groupwareEmailFoldersId',
            'groupwareEmailAccountsId',
            'userEmailAddresses'
        )

    );

    protected $_validators = array(
        /**
         * @todo document this; write a fix(?) ZF 1.5.2
         * We cannot use the Array validator, as the Zend Input will walk throgh
         * each element in the given array and check if the value is valid.
         * means, if you are passing a numeric array with values of type string,
         * validation will fail since each value of the array is being checked agains
         * the validator rule, instead of the array as a whole-
         */
        //'userEmailAddresses' => array(
            //'Array'
        //)
         'name' => array(
            'allowEmpty' => true
         ),
         'address' => array(
            'allowEmpty' => true
         ),
         'id' => array(
            'allowEmpty' => false
         ),
         'date' => array(
            'allowEmpty' => false
         ),
         'subject' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'from' => array(
            'allowEmpty' => false
         ),
         'replyTo' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'to' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'cc' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'bcc' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'inReplyTo' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'references' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'contentTextPlain' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'contentTextHtml' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'groupwareEmailFoldersId' => array(
            'allowEmpty' => false
         ),
         'groupwareEmailAccountsId' => array(
            'allowEmpty' => false
         ),
         'userEmailAddresses' => array(
            'allowEmpty' => true,
            'default'    => ''
         )
    );

    protected $_filters = array(
        'name' => array(
            'StringTrim'
        ),
        'address' => array(
            'StringTrim'
        ),
        'userEmailAddresses' => array(
            'Raw'
        ),
        'contentTextPlain' => array(

        ),
        'subject' => array(

        ),
        'from' => array(
            array('EmailRecipients', false)
        ),
        'replyTo' => array(
            array('EmailRecipients', false)
        ),
        'to' => array(
            array('EmailRecipients', false)
        ),
        'cc' => array(
            array('EmailRecipients', false)
        ),
        'bcc' => array(
            array('EmailRecipients', false)
        )
    );


    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();

        $this->_filters['contentTextPlain'][] = new Zend_Filter_HtmlEntities(
            array(
                'quotestyle' => ENT_COMPAT,
                'charset'    => 'UTF-8'
            )
        );

        $context = "";

        switch ($this->_context) {
            case self::CONTEXT_REPLY:
            case self::CONTEXT_REPLY_ALL:
                $this->_filters['subject'][] = new Conjoon_Filter_StringPrependIf(array(
                    'Re: ', 'RE: ', 'Aw: ', 'AW: '
                ), 'Re: ');
            break;

            case self::CONTEXT_FORWARD:
                $this->_filters['subject'][] = new Conjoon_Filter_StringWrap('[Fwd: ', ']');
            break;

        }

    }

    public function getProcessedData()
    {
        $data = parent::getProcessedData();

        $data['contentTextHtml']  = "";

        switch ($this->_context) {

            case self::CONTEXT_NEW:
                $name    = $data['name'];
                $address = $data['address'];

                unset($data['name']);
                unset($data['type']);
                unset($data['address']);

                $data['to'] = array();

                if ($address != "") {
                    /**
                     * @see Conjoon_Modules_Groupware_Email_Address
                     */
                    require_once 'Conjoon/Modules/Groupware/Email/Address.php';

                    if ($name === $address) {

                        $data['to'] = array(
                            new Conjoon_Modules_Groupware_Email_Address(array($address))
                        );

                    } else {
                        /**
                         * @see Conjoon_Filter_EmailRecipientsToString
                         */
                        require_once 'Conjoon/Filter/EmailRecipientsToString.php';

                        $recipientToStringFilter = new Conjoon_Filter_EmailRecipientsToString();

                        $str = $recipientToStringFilter->filter(array(array($address, $name)));

                        $data['to'] = array(
                            new Conjoon_Modules_Groupware_Email_Address(array($address, $str))
                        );
                    }
                }

                return $data;
            break;

            case self::CONTEXT_REPLY:
                if (!empty($data['replyTo'])) {
                    $data['to'] = $data['replyTo'];
                } else {
                    $data['to'] = $data['from'];
                }

                $data['cc']  = array();
                $data['bcc'] = array();
            break;

            case self::CONTEXT_REPLY_ALL:

                $data['userEmailAddresses'] = (array)$data['userEmailAddresses'];

                $merge = array();
                for ($i = 0, $len = max(count($data['cc']), count($data['to'])); $i < $len; $i++) {
                    if (isset($data['cc'][$i]) && !in_array($data['cc'][$i][0], $data['userEmailAddresses'])) {
                        $merge[] = $data['cc'][$i];
                    }
                    if (isset($data['to'][$i]) && !in_array($data['to'][$i][0], $data['userEmailAddresses'])) {
                        $merge[] = $data['to'][$i];
                    }
                }

                $data['cc'] = $merge;
                if (!empty($data['replyTo'])) {
                    $data['to'] = $data['replyTo'];
                } else {
                    $data['to'] = $data['from'];
                }
            break;

            case self::CONTEXT_EDIT:
            break;

            case self::CONTEXT_FORWARD:
                $data['inReplyTo'] = "";
                $data['to']  = array();
                $data['cc']  = array();
                $data['bcc'] = array();
            break;

        }

        unset($data['userEmailAddresses']);
        unset($data['from']);
        unset($data['replyTo']);

        /**
         * @see Conjoon_Filter_QuoteToBlockquote
         */
        require_once 'Conjoon/Filter/QuoteToBlockquote.php';

        /**
         * @see Conjoon_Filter_NormalizeLineFeeds
         */
        require_once 'Conjoon/Filter/NormalizeLineFeeds.php';

        /**
         * @see Conjoon_Filter_PlainToHtml
         */
        require_once 'Conjoon/Filter/PlainToHtml.php';

        /**
         * @see Conjoon_Filter_SignatureStrip
         */
        require_once 'Conjoon/Filter/SignatureStrip.php';

        $signatureStripper  = new Conjoon_Filter_SignatureStrip();
        $quoteFilter        = new Conjoon_Filter_QuoteToBlockquote();
        $lineFeedFilter     = new Conjoon_Filter_NormalizeLineFeeds();
        $plainToHtmlFilter  = new Conjoon_Filter_PlainToHtml();


        $data['contentTextPlain'] = $signatureStripper->filter(
            $lineFeedFilter->filter(
                $data['contentTextPlain']
            )
        );

        // if signature stripper returned empty messagem, return the data
        if (trim($data['contentTextPlain']) == "") {
            $data['contentTextPlain'] = trim($data['contentTextPlain']);
            return $data;
        }

        $startTag = "";
        $endTag   = "";

        switch ($this->_context) {
            case self::CONTEXT_REPLY:
            case self::CONTEXT_REPLY_ALL:
            case self::CONTEXT_FORWARD:
                $startTag = "<blockquote>";
                $endTag   = "</blockquote>";
            break;
        }

        $data['contentTextPlain'] = $startTag.
            $plainToHtmlFilter->filter(
                $quoteFilter->filter(
                    $data['contentTextPlain']
                )
            )
         . $endTag;

        return $data;
    }

}