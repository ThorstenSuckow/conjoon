<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @see Zend_Filter_HtmlEntities
 */
require_once 'Zend/Filter/HtmlEntities.php';

/**
 * @see Conjoon_Filter_Raw
 */
require_once 'Conjoon/Filter/Raw.php';

/**
 * A filter used for preparing data fetched from the database for sending as
 * a response to the client.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Modules_Groupware_Email
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Message_Filter_MessageResponse extends Conjoon_Filter_Input {

    protected $_presence = array(
         self::CONTEXT_RESPONSE => array(
            'id',
            'to',
            'uId',
            'path',
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
            array(
                'HtmlEntities',
                array(
                    'quotestyle' => ENT_COMPAT,
                    'charset'    => 'UTF-8'
                )
            ),
        ),
        'body' => array(
            array(
                'HtmlEntities',
                array(
                    'quotestyle' => ENT_COMPAT,
                    'charset'    => 'UTF-8'
                )
            ),
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
        'date' => array(
            'DateUtcToLocal'
        )
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();

        /**
         * @see Conjoon_Modules_Groupware_Email_Address_Filter_EmailRecipientsToAddressList
         */
        require_once 'Conjoon/Modules/Groupware/Email/Address/Filter/EmailRecipientsToAddressList.php';

        $filter = new Conjoon_Modules_Groupware_Email_Address_Filter_EmailRecipientsToAddressList();

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
             * @see Conjoon_Filter_UrlToATag
             */
            require_once 'Conjoon/Filter/UrlToATag.php';

            /**
             * @see Conjoon_Filter_QuoteToBlockquote
             */
            require_once 'Conjoon/Filter/QuoteToBlockquote.php';

            /**
             * @see Conjoon_Filter_SignatureWrap
             */
            require_once 'Conjoon/Filter/SignatureWrap.php';

            /**
             * @see Conjoon_Filter_NormalizeLineFeeds
             */
            require_once 'Conjoon/Filter/NormalizeLineFeeds.php';

            /**
             * @see Conjoon_Filter_PlainToHtml
             */
            require_once 'Conjoon/Filter/PlainToHtml.php';

            /**
             * @see Conjoon_Filter_EmoticonReplacement
             */
            require_once 'Conjoon/Filter/EmoticonReplacement.php';

            $urlFilter = new Conjoon_Filter_UrlToATag(array(
                'target' => '_blank'
            ));
            $quoteFilter     = new Conjoon_Filter_QuoteToBlockquote();
            $lineFeedFilter  = new Conjoon_Filter_NormalizeLineFeeds();
            $signatureFilter = new Conjoon_Filter_SignatureWrap(
                '<div class="signature">',
                '</div>'
            );
            $plainToHtmlFilter  = new Conjoon_Filter_PlainToHtml();
            $emoticonFilter     = new Conjoon_Filter_EmoticonReplacement(
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

            /**
             * @see Conjoon_Text_Transformer_EmailAddressToHtml
             */
            require_once 'Conjoon/Text/Transformer/EmailAddressToHtml.php';

            $transformer = new Conjoon_Text_Transformer_EmailAddressToHtml();

            $data['body'] = $transformer->transform(
                $plainToHtmlFilter->filter(
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
                )
            );
        }


        return $data;
    }


}
