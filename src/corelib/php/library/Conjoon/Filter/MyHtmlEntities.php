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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_MyHtmlEntities implements Zend_Filter_Interface
{

    /**
     * Defined by Zend_Filter_Interface
     *
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $value = str_replace(
            array('�'),
            array('&eacute;'),
            $value
        );

        return $value;
    }
}
