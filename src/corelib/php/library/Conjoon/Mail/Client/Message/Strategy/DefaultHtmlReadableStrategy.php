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
 * @see \Conjoon\Mail\Client\Message\Strategy\HtmlReadableStrategy;
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/HtmlReadableStrategy.php';

/**
 * @see \Conjoon\Mail\Client\Message\Strategy\PlainReadableStrategy;
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/PlainReadableStrategy.php';

use \Conjoon\Argument\ArgumentCheck,
    \Conjoon\Mail\Client\Message\Strategy\PlainReadableStrategy;

/**
 * Default implementation for parsing a mail's html body part
 * ans sanitizing it for safe display in a web browser.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultHtmlReadableStrategy implements HtmlReadableStrategy {

    /**
     * @type \HtmlPurifier
     */
    protected $htmlPurifier;

    /**
     * @type \Conjoon\Mail\Client\Message\Strategy\PlainReadableStrategy
     */
    protected $plainReadableStrategy;

    /**
     * Creates a new instance of HtmlReadableStrategy.
     *
     * @param \HTMLPurifier $htmlPurifier The htmlpurifier instance to sanitize html code
     * @param \Conjoon\Mail\Client\Message\Strategy\PlainReadableStrategy $plainReadableStrategy
     * the fallback strategy if no html is available and plain text should be rendered.
    *
     *
     */
    public function __construct(
        \HtmlPurifier $htmlPurifier,
        \Conjoon\Mail\Client\Message\Strategy\PlainReadableStrategy $plainReadableStrategy,
        $allowExternals = false) {
        $this->htmlPurifier = $htmlPurifier;
        $this->plainReadableStrategy = $plainReadableStrategy;
    }


    /**
     * The message text to transform can be found in $data['message']['contentTextHtml'].
     * If the message text cannot be found, the key 'contentTextPlain' will be looked up,
     * and any content found therein used as the mail message text.
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
                'contentTextHtml' => array(
                    'type' => 'string',
                    'allowEmpty' => true
                )), $data['message']);

            $text = $data['message']['contentTextHtml'];

            if ($text == "") {
                if (isset($data['message']['contentTextPlain'])) {
                    return $this->plainReadableStrategy->execute($data);
                } else {
                    return "";
                }
            }

            return $this->htmlPurifier->purify($text);


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
