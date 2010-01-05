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
 * @see Conjoon_Modules_Groupware_Email_Keys
 */
require_once 'Conjoon/Modules/Groupware/Email/Keys.php';

/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Item_Filter_ReferenceTypes implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns text/plain, text/html or multipart based on the passed parameter.
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        $str = trim((string)$value);

        return $str == "" ? array() : explode(',', $str);
    }
}
