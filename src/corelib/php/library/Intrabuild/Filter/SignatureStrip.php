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
 * @category   Filter
 * @package    Intrabuild_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Filter_SignatureStrip implements Zend_Filter_Interface
{


    /**
     * Defined by Zend_Filter_Interface
     *
     * Tries to strip a signature from a given text.
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        // if it starts with asignature, return empty string
        $index = strrpos($value, "-- \n");

        if ($index === 0) {
            return "";
        }

        $index = strpos($value, "\n-- \n");

        $value = substr($value, 0, $index);


        return $value;
    }
}