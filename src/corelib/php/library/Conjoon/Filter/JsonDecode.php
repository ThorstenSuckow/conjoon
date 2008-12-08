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
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Filter_JsonDecode implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the value json decoded
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        return Zend_Json::decode($value);
    }
}
