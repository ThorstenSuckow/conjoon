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

/**
 * @see \Conjoon\Mail\Client\Message\Strategy\ReadableStrategyResult
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/ReadableStrategyResult.php';


use \Conjoon\Argument\ArgumentCheck,
    \Conjoon\Mail\Client\Message\Strategy\PlainReadableStrategy,
    \Conjoon\Mail\Client\Message\Strategy\ReadableStrategyResult;

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
     * @type \Conjoon\Text\Parser\Html\ExternalResourcesParser
     */
    protected $externalResourcesParser;

    /**
     * Creates a new instance of HtmlReadableStrategy.
     *
     * @param \HTMLPurifier $htmlPurifier The htmlpurifier instance to sanitize html code
     * @param \Conjoon\Mail\Client\Message\Strategy\PlainReadableStrategy $plainReadableStrategy
     * the fallback strategy if no html is available and plain text should be rendered.
     * @param \Conjoon\Text\Parser\Html\ExternalResourcesParser $externalResourcesParser
     * filter for checking if external resources are available in the parsed text
     */
    public function __construct(
        \HtmlPurifier $htmlPurifier,
        PlainReadableStrategy $plainReadableStrategy,
        \Conjoon\Text\Parser\Html\ExternalResourcesParser $externalResourcesParser) {

        $this->htmlPurifier = $htmlPurifier;
        $this->plainReadableStrategy = $plainReadableStrategy;
        $this->externalResourcesParser = $externalResourcesParser;

    }

    /**
     * @inheritdoc
     */
    public function areExternalResourcesAllowed() {
        return !$this->htmlPurifier->config->get('URI.DisableExternalResources');
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
                    return new ReadableStrategyResult("", false, false);
                }
            }

            $parseResult = $this->externalResourcesParser->parse($text);
            $parseData = $parseResult->getData();

            return new ReadableStrategyResult(
                $this->htmlPurifier->purify($text),
                $parseData['externalResources'],
                $this->areExternalResourcesAllowed()
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
