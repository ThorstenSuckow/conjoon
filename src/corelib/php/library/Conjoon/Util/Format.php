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

/**
 * Utility methods for convinient access to convert/formatting methods.
 *
 * @package Conjoon_Util
 * @subpackage Util
 * @category Util
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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