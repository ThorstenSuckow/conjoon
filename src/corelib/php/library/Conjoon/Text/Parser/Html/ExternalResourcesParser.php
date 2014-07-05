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

namespace Conjoon\Text\Parser\Html;

/**
 * @see Conjoon_Text_Parser
 */
require_once 'Conjoon/Text/Parser.php';

/**
 * @see \Conjoon\Text\Parser\Html\Result\ResultExternalResourcesParseResult
 */
require_once 'Conjoon/Text/Parser/Html/Result/ExternalResourcesParseResult.php';

use \Conjoon\Text\Parser\Html\Result\ExternalResourcesParseResult;

/**
 * Parses a text and checks if there are any html elements that represent
 * embedded resources.
 *
 * @uses \Conjoon_Text_Parser
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ExternalResourcesParser extends \Conjoon_Text_Parser {

    /**
     * @return \Conjoon\Text\Parser\Html\Result\ExternalResourcesParseResult
     *
     * @inheritdoc Conjoon_Text_Parser::parse
     *
     * @see http://www.w3.org/TR/html5/embedded-content-0.html#embedded-content-0
     */
    public function parse($input)
    {
        $input = trim($input);

        if ($input == "") {
            return new ExternalResourcesParseResult(false);
        }

        $doc = new \DOMDocument();
        @$doc->loadHTML($input);

        $lookup = array(
            'img', 'iframe', 'embed', 'object', 'video', 'audio',
            'source', 'track', 'link'
        );

        foreach ($lookup as $tagName) {
            $tags = $doc->getElementsByTagName($tagName);

            if ($tagName != 'link') {
                foreach($tags as $tag) {

                    // exit and return ParseResult, externals resource found
                    return new ExternalResourcesParseResult(true);
                }
            }

            // tag name is link, check for rel/type if points to external
            // css
            foreach($tags as $tag) {

                if ($tag->hasAttribute('rel') &&
                    strtolower($tag->getAttribute('rel')) == 'stylesheet') {
                    return new ExternalResourcesParseResult(true);
                } else if ($tag->hasAttribute('type') &&
                    strtolower($tag->getAttribute('type')) == 'text/css') {
                    return new ExternalResourcesParseResult(true);
                }
            }
        }

        return new ExternalResourcesParseResult(false);

    }


}
