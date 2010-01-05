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
class Conjoon_Filter_PositiveArrayValues implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns an array with onlay positive values as found in the org array.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $value = (array)$value;

        $wlist = array();

        for ($i = 0, $len = count($value); $i < $len; $i++) {
            $t = (int)$value[$i];
            if ($t > 0) {
                $wlist[] = $t;
            }
        }

        return $wlist;
    }
}
