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
        if (is_string($value)) {
            /**
             * @see Zend_Json
             */
            require_once 'Zend/Json.php';

            return Zend_Json::decode($value);
        }

        return $value;
    }
}
