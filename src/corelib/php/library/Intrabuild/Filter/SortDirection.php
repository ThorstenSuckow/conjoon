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
class Intrabuild_Filter_SortDirection implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns either ASC or DESC based on the passed parameter.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $str = trim(strtolower((string)$value));

        switch ($str) {
            case 'asc':
                return 'ASC';
            case 'desc':
                return 'DESC';
            default:
                return 'ASC';

        }
    }
}
