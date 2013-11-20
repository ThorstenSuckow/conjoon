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

namespace Conjoon\Mail\Client\Message\Strategy;

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see \Conjoon\Mail\Client\Message\Strategy\ReadableStrategy;
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/ReadableStrategy.php';

use \Conjoon\Argument\ArgumentCheck;

/**
 * Default implementation for parsing a mail body to plain format along with
 * link formatting, emoticons etc.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class PlainReadableStrategy implements ReadableStrategy {

    /**
     * The message text to transform can be found in $data['message']['contentTextPlain'].
     *
     * @inheritdoc
     */
    public function execute(array $data) {

        try {

            ArgumentCheck::check(array(
                'message' => array(
                    'type' => 'array',
                    'allowEmpty' => false
                )), $data);

            ArgumentCheck::check(array(
                'contentTextPlain' => array(
                    'type' => 'string',
                    'allowEmpty' => true
                )), $data['message']);

            $text = $data['message']['contentTextPlain'];

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

        } catch (\Exception $e) {

            /**
             * @see \Conjoon\Mail\Client\Message\Strategy\StrategyException;
             */
            require_once 'Conjoon/Mail/Client/Message/Strategy/StrategyException.php';

            throw new StrategyException(
                "Exception thrown by previous exception", 0, $e
            );

        }

    }


}
