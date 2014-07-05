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
 * Expects a string with an email address identity (email address with or
 * without a name). Returns an array with address/name pairs, or just the
 * address if no name was found.
 *
 * Example:
 *
 * INPUT:
 * ======
 * "\"Thorsten Suckow-Homberg\" <tsuckow@conjoon.org>, yo@mtv.com"
 *
 * Output:
 * =======
 * [
 *  ["tsuckow@conjoon.org", "Thorsten Suckow-Homberg"],
 *  [yo@mtv.com]
 * ]
 *
 *
 * @uses Conjoon_Text_Parser
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Parser_Mail_EmailAddressIdentityParser extends Conjoon_Text_Parser {

    protected $_addSlashes;
    protected $_useQuoting;

    public function __construct($options = array())
    {
        $this->_options = $options;

        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';

        Conjoon_Util_Array::applyIf($this->_options, array(
            'addSlashes' => true,
            'useQuoting' => true
        ));

        $this->_addSlashes = $this->_options['addSlashes'];
        $this->_useQuoting = $this->_options['useQuoting'];
    }

    /**
     * @inherit Conjoon_Text_Parser::parse
     */
    public function parse($input)
    {


        $value = (array)$input;

        $addr = array();

        $pattern = '/(^|\s|,)*+(([^,"]*".*?"[^,"]*)\s*<(.*?)>|([^",]+)\s*<(.*?)>|([^,\s"<>]+))[,\s$]?/msi';

        $a = 0;
        for ($i = 0, $len = count($value); $i < $len; $i++) {

            // normalize the string - replace linebreaks, tabs and leading/trailing whitespace/commas
            $value[$i] = preg_replace(
                "/^[\s,]+|[\s,]+$|[\t\r\n]/",
                '',
                $value[$i]
            );

            if (trim($value[$i]) == "") {
                continue;
            }

            preg_match_all($pattern, $value[$i], $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                if (isset($match[7])) {
                    $addr[$a] = array(trim($match[7]));
                } else if (isset($match[6])) {
                    $addr[$a] = array(trim($match[6]), trim($match[5]));
                } else {
                    $addr[$a] = array(trim($match[4]), trim($match[3]));
                }

                if ($this->_addSlashes === true && isset($addr[$a][1])) {
                    $temp = $addr[$a][1];

                    // assume the name is quoted, add quotes to the whole name
                    // i.e. 'Thorsten \"Suckow-Homberg\"' becomes '"Thorsten \"Suckow-Homberg\""'
                    // quote only if it's not already quoted!
                    if (strpos($temp, '\"') !== false) {
                        if  (substr($temp, 0, 1) != '"' || substr($temp, -1) != '"') {
                            $addr[$a][1] = '"' . $temp . '"';
                        }
                    } else if (strpos(trim($temp, '"'), '"') !== false) {
                        // assume the name is quoted, without escaping
                        // i.e. 'Thorsten "Suckow-Homberg"' becomes '"Thorsten \"Suckow-Homberg\""'
                        $addr[$a][1] = '"' . addslashes($temp) . '"';
                    } else if (preg_match('/[,@\[\];"]/', trim($temp, '"')) === 0) {
                        // we want only the name!!! If it does not need to be quoted, don't quote it!
                        $addr[$a][1] = trim($temp, '"');
                    }

                } else if ($this->_addSlashes === false && isset($addr[$a][1])) {
                    // asumme the string is quoted since escaped quotes are found
                    // the filter assumes that escaped quotes only occure if and only
                    // if the whole string is quoted
                    if (strpos($addr[$a][1], '\"') !== false) {
                        $temp = stripslashes($addr[$a][1]);

                        if (strpos($temp, '"') === 0) {
                            $temp = substr($temp, 1);
                        }
                        if (strrpos($temp, '"') === strlen($temp)-1) {
                            $temp = substr($temp, 0, -1);
                        }

                        // find the first quote, check if there is anything that definitely
                        // needs to be quoted
                        if ($this->_useQuoting !== false) {
                            $t2 = substr($temp, 0, strpos($temp, '"'));
                            $t3 = substr($temp, strrpos($temp, '"'));

                            if (preg_match('/[,@\[\];]/', $t2) !== 0 || preg_match('/[,@\[\];]/', $t3) !== 0) {
                                //leave anything as is
                                $temp = $addr[$a][1];
                            }
                        }

                        $addr[$a][1] = $temp;

                    } else if (strpos($addr[$a][1], '"') !== false) {
                        // assume the string is quoted and can be unquoted safely
                        // since not escaped quotes occure, except if any of the following chars
                        // can be found: [,@\[\];]
                        if ($this->_useQuoting === false || preg_match('/[,@\[\];]/', $addr[$a][1]) == 0) {
                            $addr[$a][1] = trim($addr[$a][1], '"');
                        }
                    }
                }

                $a++;
            }
        }

        return $addr;
    }

}