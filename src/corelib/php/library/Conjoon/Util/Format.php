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
 * Utility methods for convinient access to convert/formatting methods.
 *
 * @package Conjoon_Util
 * @subpackage Util
 * @category Util
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Util_Format  {

    /**
     * Enforce static behavior.
     */
    private function __construct()
    {

    }

    /**
     * Converts a (kilo/mega/giga)byte value to an integer value, representing the
     * value in bytes.
     * If no valid quantifier (k, m, g) was found, the passed argument will be
     * casted to int and returned.
     *
     * @param mixed $value
     *
     * @return int
     */
    public static function convertToBytes($value)
    {
        if (is_numeric($value)) {
            return $value;
        } else {
            $len  = strlen($value);
            $num  = substr($value, 0, $len-1);
            $unit = strtolower(substr($value, $len-1));
            switch ( $unit ) {
                case 'k':
                    return $num * 1024;
                case 'm':
                    return $num * 1048576;
                case 'g':
                    return $num * 1073741824;
                default:
                    return (int)$value;
            }
    }
}

}