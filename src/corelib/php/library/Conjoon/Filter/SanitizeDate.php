<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
class Conjoon_Filter_sanitizeDate implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Sanitizes a date string so Zend_Date is capable of parsing it.
     * For example, changes
     * Wed, 6 May 2009 20:38:58 +0000 (GMT+00:00)
     * to
     * Wed, 6 May 2009 20:38:58 +0000
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $regex = '/(\(.*\))$/';
        $str = preg_replace($regex, "", $value);

        return trim($str);
    }
}
