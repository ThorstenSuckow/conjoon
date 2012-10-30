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
 * @see Conjoon_Modules_Groupware_Email_Keys
 */
require_once 'Conjoon/Modules/Groupware/Email/Keys.php';

/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Draft_Filter_DraftFormatFilter implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns text/plain, text/html or multipart based on the passed parameter.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $str = trim((string)$value);

        switch ($str) {
            case Conjoon_Modules_Groupware_Email_Keys::FORMAT_TEXT_PLAIN:
            case Conjoon_Modules_Groupware_Email_Keys::FORMAT_TEXT_HTML:
            case Conjoon_Modules_Groupware_Email_Keys::FORMAT_MULTIPART:
                return $str;
            default:
                return Conjoon_Modules_Groupware_Email_Keys::FORMAT_TEXT_PLAIN;
        }
    }
}
