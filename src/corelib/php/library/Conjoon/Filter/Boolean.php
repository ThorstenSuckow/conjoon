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
class Conjoon_Filter_Boolean implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Simply casts to boolean
     *
     * @param  mixed $value
     * @return boolean
     */
    public function filter($value)
    {
        return (bool)$value;
    }
}
