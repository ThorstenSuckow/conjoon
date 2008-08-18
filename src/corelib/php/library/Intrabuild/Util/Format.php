<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Array.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Util/Array.php $
 */

/**
 * Utility methods for convinient access to convert/formatting methods.
 *
 * @package Intrabuild_Util
 * @subpackage Util
 * @category Util
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Util_Format  {

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