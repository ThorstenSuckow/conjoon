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
 * @see Intrabuild_Filter_Input
 */
require_once 'Intrabuild/Filter/Input.php';

/**
 * @see Zend_Filter_HtmlEntities
 */
require_once 'Zend/Filter/HtmlEntities.php';

/**
 * @see Intrabuild_Filter_Raw
 */
require_once 'Intrabuild/Filter/Raw.php';

/**
 * A filter used for preparing data fetched from the database for sending as
 * a response to the client.
 *
 * @uses Intrabuild_Filter_Input
 * @package    Intrabuild_Modules_Groupware_Email
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Message_Filter_MessageResponse extends Intrabuild_Filter_Input {

    protected $_presence = array(
         self::CONTEXT_RESPONSE => array(
            'id',
            'to',
            'cc',
            'bcc',
            'replyTo',
            'from',
            'subject',
            'body',
            'date',
            'isSpam',
            'isPlainText',
            'groupwareEmailFoldersId'
        )
    );

    protected $_filters = array(
        'subject' => array(
            array('Htmlentities', ENT_COMPAT, 'UTF-8'),
        ),
        'body' => array(
            array('Htmlentities', ENT_COMPAT, 'UTF-8'),
        ),
        'from' => array(
            array('EmailRecipients', false, false)
        ),
        'replyTo' => array(
            array('EmailRecipients', false, false)
        ),
        'to' => array(
            array('EmailRecipients', false, false)
        ),
        'cc' => array(
            array('EmailRecipients', false, false)
        ),
        'bcc' => array(
            array('EmailRecipients', false, false)
        ),

    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Intrabuild_Filter_Raw();

        /**
         * @see Intrabuild_Modules_Groupware_Email_Address_Filter_EmailRecipientsToAddressList
         */
        require_once 'Intrabuild/Modules/Groupware/Email/Address/Filter/EmailRecipientsToAddressList.php';

        $filter = new Intrabuild_Modules_Groupware_Email_Address_Filter_EmailRecipientsToAddressList();

        $this->_filters['from'][]    = $filter;
        $this->_filters['replyTo'][] = $filter;
        $this->_filters['to'][]      = $filter;
        $this->_filters['cc'][]      = $filter;
        $this->_filters['bcc'][]     = $filter;
    }

    public function getProcessedData()
    {
        $data = parent::getProcessedData();

        if ($data['body'] == "") {
            $data['body'] = " ";
        } else {

            /**
             * @see Intrabuild_Filter_UrlToATag
             */
            require_once 'Intrabuild/Filter/UrlToATag.php';

            /**
             * @see Intrabuild_Filter_QuoteToBlockquote
             */
            require_once 'Intrabuild/Filter/QuoteToBlockquote.php';

            /**
             * @see Intrabuild_Filter_SignatureWrap
             */
            require_once 'Intrabuild/Filter/SignatureWrap.php';

            /**
             * @see Intrabuild_Filter_NormalizeLineFeeds
             */
            require_once 'Intrabuild/Filter/NormalizeLineFeeds.php';

            /**
             * @see Intrabuild_Filter_PlainToHtml
             */
            require_once 'Intrabuild/Filter/PlainToHtml.php';

            /**
             * @see Intrabuild_Filter_EmoticonReplacement
             */
            require_once 'Intrabuild/Filter/EmoticonReplacement.php';

            $urlFilter = new Intrabuild_Filter_UrlToATag(array(
                'target' => '_blank'
            ));
            $quoteFilter     = new Intrabuild_Filter_QuoteToBlockquote();
            $lineFeedFilter  = new Intrabuild_Filter_NormalizeLineFeeds();
            $signatureFilter = new Intrabuild_Filter_SignatureWrap(
                '<div class="signature">',
                '</div>'
            );
            $plainToHtmlFilter  = new Intrabuild_Filter_PlainToHtml();
            $emoticonFilter     = new Intrabuild_Filter_EmoticonReplacement(
                array(
                    'O:-)'    => '<span class="emoticon innocent"></span>',
                    ':-)'     => '<span class="emoticon smile"></span>',
                    ':)'      => '<span class="emoticon smile"></span>',
                    ':-D'     => '<span class="emoticon laughing"></span>',
                    ':D'      => '<span class="emoticon laughing"></span>',
                    ':-('     => '<span class="emoticon frown"></span>',
                    ':('      => '<span class="emoticon frown"></span>',
                    ':-['     => '<span class="emoticon embarassed"></span>',
                    ';-)'     => '<span class="emoticon wink"></span>',
                    ';)'      => '<span class="emoticon wink"></span>',
                    ':-\\'    => '<span class="emoticon undecided"></span>',
                    ':-P'     => '<span class="emoticon tongue"></span>',
                    ';-P'     => '<span class="emoticon tongue"></span>',
                    ':P'      => '<span class="emoticon tongue"></span>',
                    '=-O'     => '<span class="emoticon surprise"></span>',
                    ':-*'     => '<span class="emoticon kiss"></span>',
                    ':*'      => '<span class="emoticon kiss"></span>',
                    '&gt;:o'  => '<span class="emoticon yell"></span>',
                    '&gt;:-o' => '<span class="emoticon yell"></span>',
                    '8-)'     => '<span class="emoticon cool"></span>',
                    ':-$'     => '<span class="emoticon money"></span>',
                    ':-!'     => '<span class="emoticon foot"></span>',
                    ':\'('    => '<span class="emoticon cry"></span>',
                    ':-X'     => '<span class="emoticon sealed"></span>'
            ));

            $data['body'] = $plainToHtmlFilter->filter(
                $signatureFilter->filter(
                    $quoteFilter->filter(
                        $urlFilter->filter(
                            $emoticonFilter->filter(
                                $lineFeedFilter->filter(
                                    $data['body']
                                )
                            )
                        )
                    )
                )
            );
        }


        return $data;
    }


}