<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Intrabuild_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Filter_Base64Decode implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the base64-decoed representation of this value. The return value
     * might be binary, so use only binary-safe oprations on it.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $str = trim((string)$value);
        if ($str == "") {
            return $value;
        }

        $str = str_replace("\r\n", "", $str);
        $str = str_replace("\r", "", $str);
        $str = str_replace("\n", "", $str);

        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $str)) {
            return base64_decode($str);
        } else {
            return $value;
        }
    }
}
