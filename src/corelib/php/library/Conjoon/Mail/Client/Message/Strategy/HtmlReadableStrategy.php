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
 * Default implementation for parsing a mail's html body part
 * ans sanitizing it for safe display in a web browser.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class HtmlReadableStrategy implements ReadableStrategy {

    /**
     * @inheritdoc
     */
    public function execute(array $data) {

        try {

            ArgumentCheck::check(array(
                'text' => array(
                    'type' => 'string',
                    'allowEmpty' => false
                )), $data);

            $text = $data['text'];

            if ($text == "") {
                return "";
            }

            return 'fail';

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
