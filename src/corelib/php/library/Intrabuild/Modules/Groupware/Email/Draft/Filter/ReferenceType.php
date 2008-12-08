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
 * @see Intrabuild_Modules_Groupware_Email_Keys
 */
require_once 'Intrabuild/Modules/Groupware/Email/Keys.php';

/**
 * @category   Filter
 * @package    Intrabuild_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Draft_Filter_ReferenceType implements Zend_Filter_Interface
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

        switch ($str) {

            case Intrabuild_Modules_Groupware_Email_Keys::REFERENCE_TYPE_NEW:
            case Intrabuild_Modules_Groupware_Email_Keys::REFERENCE_TYPE_EDIT:
            case Intrabuild_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY:
            case Intrabuild_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY_ALL:
            case Intrabuild_Modules_Groupware_Email_Keys::REFERENCE_TYPE_FORWARD:
                return $str;
            default:
                return Intrabuild_Modules_Groupware_Email_Keys::REFERENCE_TYPE_NEW;
        }
    }
}
