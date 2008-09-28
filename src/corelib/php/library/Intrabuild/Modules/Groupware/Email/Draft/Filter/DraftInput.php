<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Request.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Modules/Groupware/Email/Item/Filter/Request.php $
 */

/**
 * @see Intrabuild_Filter_Input
 */
require_once 'Intrabuild/Filter/Input.php';

/**
 * @see Intrabuild_Filter_Raw
 */
require_once 'Intrabuild/Filter/Raw.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Keys
 */
require_once 'Intrabuild/Modules/Groupware/Email/Keys.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Draft_Filter_DraftFormatFilter
 */
require_once 'Intrabuild/Modules/Groupware/Email/Draft/Filter/DraftFormatFilter.php';


/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating feed items.
 *
 * @uses Intrabuild_Filter_Input
 * @package    Intrabuild_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Draft_Filter_DraftInput extends Intrabuild_Filter_Input {

    protected $_presence = array(

        self::CONTEXT_CREATE => array(
            'format',
            'id',
            'date',
            'groupwareEmailAccountsId',
            'groupwareEmailFoldersId',
            'subject',
            'inReplyTo',
            'references',
            'message',
            'to',
            'cc',
            'bcc',
            'type'
        )

    );

    protected $_filters = array(
        'type' => array(),
        'format' => array(),
         'id' => array(
            'Int'
         ),
         'groupwareEmailAccountsId' => array(
            'Int'
         ),
         'date' => array(
            'Int'
         ),
         'groupwareEmailFoldersId' => array(
            'Int'
         ),
        'subject' => array(
            'Raw'
        ),
        'message' => array(
            'Raw'
        ),
        'references' => array(
            'Raw'
        ),
        'inReplyTo' => array(
            'Raw'
        ),
        'to' => array(
            'JsonDecode',
            'EmailRecipients'
        ),
        'cc' => array(
            'JsonDecode',
            'EmailRecipients'
        ),
        'bcc' => array(
            'JsonDecode',
            'EmailRecipients'
        )
    );

    protected $_validators = array(
        'format' => array(
            'allowEmpty' => false
         ),
         'type' => array(
            'allowEmpty' => false
         ),
         'id' => array(
            'allowEmpty' => false,
            array('GreaterThan', -2)
         ),
         'date' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
        'groupwareEmailAccountsId' => array(
            'allowEmpty' => false,
             array('GreaterThan', 0)
         ),
        'groupwareEmailFoldersId' => array(
            'allowEmpty' => false,
            array('GreaterThan', -2)
         ),
         'subject' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
        'message' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'references' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'inReplyTo' => array(
            'allowEmpty' => true,
            'default'    => ''
         ),
         'to' => array(
            'allowEmpty' => true
          ),
         'cc' => array(
            'allowEmpty' => true
         ),
         'bcc' => array(
            'allowEmpty' => true
         )
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Intrabuild_Filter_Raw();

        /**
         * @see Intrabuild_Modules_Groupware_Email_Draft_Filter_ReferenceType
         */
        require_once 'Intrabuild/Modules/Groupware/Email/Draft/Filter/ReferenceType.php';

        $this->_filters['type'][] = new Intrabuild_Modules_Groupware_Email_Draft_Filter_ReferenceType();

        $this->_filters['format'] = array(
            new Intrabuild_Modules_Groupware_Email_Draft_Filter_DraftFormatFilter()
        );

        if ($this->_data['to'] == "" && $this->_data['cc'] == "" && $this->_data['bcc'] == "") {
            $this->_validators['to']['allowEmpty'] = false;
        }

    }

    public function getProcessedData()
    {
        $data = parent::getProcessedData();

        $message = $data['message'];
        $format  = $data['format'];

        unset($data['message']);
        unset($data['format']);

        $data['contentTextHtml'] = "";
        $data['contentTextPlain'] = "";

        if (trim($message) != "") {

            /**
             * @see Intrabuild_Filter_DraftToText
             */
            require_once 'Intrabuild/Filter/DraftToText.php';

            $draftToTextFilter = new Intrabuild_Filter_DraftToText();

            /**
             * @see Intrabuild_Filter_BlockquoteToQuote
             */
            require_once 'Intrabuild/Filter/BlockquoteToQuote.php';

            $blockquoteToQuoteFilter = new Intrabuild_Filter_BlockquoteToQuote();

            /**
             * @see Intrabuild_Filter_NormalizeLineFeeds
             */
            require_once 'Intrabuild/Filter/NormalizeLineFeeds.php';

            $normalizeLineFeedsFilter = new Intrabuild_Filter_NormalizeLineFeeds();

            /**
             * @see Intrabuild_Filter_DraftToHtml
             */
            require_once 'Intrabuild/Filter/DraftToHtml.php';

            $draftToHtmlFilter = new Intrabuild_Filter_DraftToHtml();

            switch ($format) {
                case Intrabuild_Modules_Groupware_Email_Keys::FORMAT_TEXT_PLAIN:
                    $data['contentTextPlain'] = $draftToTextFilter->filter(
                        $blockquoteToQuoteFilter->filter(
                            $normalizeLineFeedsFilter->filter(
                                str_replace(
                                    array("&nbsp;", "<br>", "<br/>", "<br />"),
                                    array(" ", "\n"),
                                    $message
                                )
                            )
                        )
                    );
                break;
                case Intrabuild_Modules_Groupware_Email_Keys::FORMAT_TEXT_HTML:
                    $data['contentTextHtml'] = $draftToHtmlFilter->filter($message);
                break;
                case Intrabuild_Modules_Groupware_Email_Keys::FORMAT_MULTIPART:
                    $data['contentTextHtml']  = $draftToHtmlFilter->filter($message);
                    $data['contentTextPlain'] = $draftToTextFilter->filter(
                        $blockquoteToQuoteFilter->filter(
                            $normalizeLineFeedsFilter->filter(
                                str_replace(
                                    array("&nbsp;", "<br>", "<br/>", "<br />"),
                                    array(" ", "\n"),
                                    $message
                                )
                            )
                        )
                    );
                break;
            }
        }

        return $data;
    }

}