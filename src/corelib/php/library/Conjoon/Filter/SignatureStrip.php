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
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Filter_SignatureStrip implements Zend_Filter_Interface
{


    /**
     * Defined by Zend_Filter_Interface
     *
     * Tries to strip a signature from a given text.
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        // if it starts with asignature, return empty string
        $index = strrpos($value, "-- \n");

        if ($index === 0) {
            return "";
        }

        $index = strpos($value, "\n-- \n");

        if ($index === false) {
            return $value;
        }

        $value = substr($value, 0, $index);

        return $value;
    }
}