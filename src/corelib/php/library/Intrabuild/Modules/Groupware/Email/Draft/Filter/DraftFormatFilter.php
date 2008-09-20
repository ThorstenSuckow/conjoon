<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: SortDirection.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Filter/SortDirection.php $
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
class Intrabuild_Modules_Groupware_Email_Draft_Filter_DraftFormatFilter implements Zend_Filter_Interface
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
            case Intrabuild_Modules_Groupware_Email_Keys::FORMAT_TEXT_PLAIN:
            case Intrabuild_Modules_Groupware_Email_Keys::FORMAT_TEXT_HTML:
            case Intrabuild_Modules_Groupware_Email_Keys::FORMAT_MULTIPART:
                return $str;
            default:
                return Intrabuild_Modules_Groupware_Email_Keys::FORMAT_TEXT_PLAIN;
        }
    }
}
