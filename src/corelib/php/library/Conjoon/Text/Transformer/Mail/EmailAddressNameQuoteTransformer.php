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
 * @see Conjoon_Text_Transformer
 */
require_once 'Conjoon/Text/Transformer.php';

/**
 * Quotes a name if needed to be used as the name for an email address.
 *
 * @uses Conjoon_Text_Transformer
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_Mail_EmailAddressNameQuoteTransformer
    extends Conjoon_Text_Transformer {



    /**
     * @inherit Conjoon_Text_Transformer::transform
     */
    public function transform($input)
    {
        $pattern = '/[,@\[\];"]/';

        $hit = preg_match($pattern, $input);

        if ($hit != 0) {
            // quote only if the string is not already quoted
            if  (substr($input, 0, 1) != '"' || substr($input, -1) != '"') {
                // if the string needs quoting, check if the quotes within the string
                // are already escaped
                if (strpos(trim($input, '"'), '\"') === false
                    && strpos(trim($input, '"'), '"') !== false) {
                    $input = str_replace('"', '\"', $input);
                }
                $input = '"' . $input . '"';
            }
        }

        return $input;
    }

}