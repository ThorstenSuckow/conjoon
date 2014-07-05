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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @deprecated use Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
 */
class Conjoon_Filter_EmailRecipients implements Zend_Filter_Interface
{
    private $_addslashes;
    private $_useQuoting;

    /**
     * Constructor.
     *
     */
    public function __construct($addSlashes = true, $useQuoting = true)
    {
        $this->_addSlashes = $addSlashes;
        $this->_useQuoting = $useQuoting;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Expects an array with recipients for an email address. Returns an array
     * with address/name pairs.
     *
     * Input:
     * [
     *  "\"Thorsten Suckow-Homberg\" <tsuckow@conjoon.org>, yo@mtv.com",
     *  "\"Pit Bull\" <pit@doggydog.com>",
     * ]
     *
     * Returns:
     * [
     *  ["tsuckow@conjoon.org", "Thorsten Suckow-Homberg"],
     *  ["yo@mtv.com"],
     *  ["pit@doggydog.com", "Pit Bull"],
     * ]
     *
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        /**
         * @see Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
         */
        require_once 'Conjoon/Text/Parser/Mail/EmailAddressIdentityParser.php';

        $parser = new Conjoon_Text_Parser_Mail_EmailAddressIdentityParser();

        $value = (array)$value;

        $data = array();
        for ($i = 0, $len = count($value); $i < $len; $i++) {
            $data = array_merge($data, $parser->parse($value[$i]));
        }

        return $data;
    }


}
