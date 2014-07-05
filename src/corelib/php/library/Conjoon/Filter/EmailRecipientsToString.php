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
 */
class Conjoon_Filter_EmailRecipientsToString implements Zend_Filter_Interface
{
    private $_useQuoting = true;

    /**
     * Constructor.
     *
     */
    public function __construct($useQuoting = true)
    {
        $this->_useQuoting = $useQuoting;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Expects an array with recipients for an email address as returned by Conjoon_Filter_EmailRecipients.
     * Returns a comma separated string with the name values of this array, or the email address if no name value was
     * found.
     *
     * Input:
     * [
     *  ["tsuckow@conjoon.org", "Thorsten Suckow-Homberg"],
     *  ["yo@mtv.com"],
     *  ["pit@doggydog.com", "Pit Bull"],
     * ]

     *
     * Returns:
     * "Thorsten Suckow-Homberg, yo@mtv.com, Pit Bull"
     *
     *
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $parts = array();

        $pattern = '/[,@\[\];"]/';

        /**
         * @see Conjoon_Text_Transformer_EmailAddressNameQuoteTransformer
         */
        require_once 'Conjoon/Text/Transformer/Mail/EmailAddressNameQuoteTransformer.php';

        $transformer = new Conjoon_Text_Transformer_Mail_EmailAddressNameQuoteTransformer();

        foreach ($value as $address) {
            if (isset($address[1])) {

                $hit = $this->_useQuoting ? preg_match($pattern, $address[1]) : 0;

                if ($hit != 0) {

                    $parts[] = $transformer->transform($address[1]);

                } else {
                    $parts[] = $address[1];
                }

                continue;
            }

            $parts[] = $address[0];
        }

        return implode(', ', $parts);
    }


}
