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
 * @see Conjoon_Text_Parser
 */
require_once 'Conjoon/Text/Parser.php';

/**
 * Parses a json string and returns an array of arrays. Each array contains the
 * key/value pairs:
 * id
 * [isRead/isSpam]
 *
 * Example:
 *
 * Input
 * =====
 * [{"id":"173","isRead":false},{"id":"172","isRead":false}]
 *
 * Output
 * ======
 * array(
 *     array('id' => '173', 'isRead' => false),
 *     array('id' => '172', 'isRead' => false)
 * )
 *
 *
 * @uses Conjoon_Text_Parser
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Parser_Mail_ClientMessageFlagListParser extends Conjoon_Text_Parser {


    /**
     * @inherit Conjoon_Text_Parser::parse
     */
    public function parse($input)
    {
        $data = array('input' => $input);

        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        Conjoon_Argument_Check::check(array(
            'input' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $data);

        $input = $data['input'];

        $parts = @json_decode($input, true);

        if ($parts === null) {
            /**
             * @see Conjoon_Text_Parser_Exception
             */
            require_once 'Conjoon/Text/Parser/Exception.php';

            throw new Conjoon_Text_Parser_Exception(
                "Could not decode \"$input\". No valid json?"
            );
        }

        return $parts;
    }

}