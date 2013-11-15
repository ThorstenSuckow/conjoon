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


namespace Conjoon\Mail\Client\Service\ServicePatron;

use Conjoon\Lang\MissingKeyException;

/**
 * @see \Conjoon\Lang\MissingKeyException
 */
require_once 'Conjoon/Lang/MissingKeyException.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServicePatron\AbstractServicePatron
 */
require_once 'Conjoon/Mail/Client/Service/ServicePatron/AbstractServicePatron.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServicePatron\ServicePatronException
 */
require_once 'Conjoon/Mail/Client/Service/ServicePatron/ServicePatronException.php';

/**
 * A service patron for reading an email message.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ReadMessagePatron
    extends \Conjoon\Mail\Client\Service\ServicePatron\AbstractServicePatron {

    /**
     * @var \Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
     */
    protected $identityParser;

    /**
     * @inheritdoc
     */
    public function applyForData(array $data)
    {
        try {

            $this->v('message', $data);

            $d =& $data['message'];

            $d['isPlainText'] = 1;
            $d['body']        = $this->createBody($this->v('contentTextPlain', $d));

            $d['attachments'] = $this->createAttachments($this->v('attachments', $d));

            /**
             * @see \Conjoon_Date_Format
             */
            require_once 'Conjoon/Date/Format.php';

            $date = $this->v('date', $d);
            $date = $date ? $date->format('Y-m-d H:i:s') : null;

            $d['date'] = \Conjoon_Date_Format::utcToLocal($date);

            $d['to']      = $this->createAddressList($this->v('to', $d));
            $d['cc']      = $this->createAddressList($this->v('cc', $d));
            $d['from']    = $this->createAddressList($this->v('from', $d));
            $d['bcc']     = $this->createAddressList($this->v('bcc', $d));
            $d['replyTo'] = $this->createAddressList($this->v('replyTo', $d));

            /**
             * @see \Zend_Filter_HtmlEntities
             */
            require_once 'Zend/Filter/HtmlEntities.php';

            $htmlEntitiesFilter = new \Zend_Filter_HtmlEntities(array(
                'quotestyle' => ENT_COMPAT
            ));

            $d['subject'] = $htmlEntitiesFilter->filter($this->v('subject', $d));

            unset($d['contentTextPlain']);
            unset($d['contentTextHtml']);

        } catch (\Exception $e) {
            throw new ServicePatronException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

        return $data;
    }

// -------- helper

    /**
     * Creates the attachments.
     *
     * @param array
     * @return array
     *
     */
    protected function createAttachments(array $attachments)
    {
        $data = array();

        for ($i = 0, $len = count($attachments); $i < $len; $i++) {
            $att =& $attachments[$i];

            $data[] = array(
                'fileName' => $att['fileName'],
                'mimeType' => $att['mimeType'],
                'key'      => $att['key']
            );
        }

        return $data;
    }

    /**
     *
     * @param $text
     *
     * @return $text
     */
    protected function createBody($text)
    {
        if ($text == "") {
            return "";
        }

        /**
         * @see \Conjoon_Filter_UrlToATag
         */
        require_once 'Conjoon/Filter/UrlToATag.php';

        /**
         * @see \Conjoon_Filter_QuoteToBlockquote
         */
        require_once 'Conjoon/Filter/QuoteToBlockquote.php';

        /**
         * @see \Conjoon_Filter_SignatureWrap
         */
        require_once 'Conjoon/Filter/SignatureWrap.php';

        /**
         * @see \Conjoon_Filter_NormalizeLineFeeds
         */
        require_once 'Conjoon/Filter/NormalizeLineFeeds.php';

        /**
         * @see \Conjoon_Filter_PlainToHtml
         */
        require_once 'Conjoon/Filter/PlainToHtml.php';

        /**
         * @see \Conjoon_Filter_EmoticonReplacement
         */
        require_once 'Conjoon/Filter/EmoticonReplacement.php';

        $urlFilter = new \Conjoon_Filter_UrlToATag(array(
            'target' => '_blank'
        ));
        $quoteFilter     = new \Conjoon_Filter_QuoteToBlockquote();
        $lineFeedFilter  = new \Conjoon_Filter_NormalizeLineFeeds();
        $signatureFilter = new \Conjoon_Filter_SignatureWrap(
            '<div class="signature">',
            '</div>'
        );
        $plainToHtmlFilter  = new \Conjoon_Filter_PlainToHtml();
        $emoticonFilter     = new \Conjoon_Filter_EmoticonReplacement(
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
         * @see \Conjoon_Text_Transformer_EmailAddressToHtml
         */
        require_once 'Conjoon/Text/Transformer/EmailAddressToHtml.php';

        $transformer = new \Conjoon_Text_Transformer_EmailAddressToHtml();

        /**
         * @see \Zend_Filter_HtmlEntities
         */
        require_once 'Zend/Filter/HtmlEntities.php';

        $zfe  = new \Zend_Filter_HtmlEntities(
            array(
                'quotestyle' => ENT_COMPAT/*,
                'charset'    => 'UTF-8'*/
            )
        );

        return $transformer->transform(
            $plainToHtmlFilter->filter(
                $signatureFilter->filter(
                    $quoteFilter->filter(
                        $urlFilter->filter(
                            $emoticonFilter->filter(
                                $lineFeedFilter->filter(
                                    $zfe->filter($text)
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    /**
     *
     * @param string $text
     *
     * @return array
     */
    protected function createAddressList($text)
    {
        if (!$this->identityParser) {
            /**
             * @see Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
             */
            require_once 'Conjoon/Text/Parser/Mail/EmailAddressIdentityParser.php';

            $this->identityParser = new \Conjoon_Text_Parser_Mail_EmailAddressIdentityParser();
        }

        $res = $this->identityParser->parse($text);

        $addresses = array();

        foreach ($res as $values) {
            $addresses[] = array(
                'address' => isset($values[0]) ? $values[0] : '',
                'name'    => isset($values[1]) ? $values[1] : '',

            );
        }

        return array(
            'addresses' => $addresses
        );

    }
}
