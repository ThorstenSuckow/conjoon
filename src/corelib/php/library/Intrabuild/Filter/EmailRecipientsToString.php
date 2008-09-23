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
class Intrabuild_Filter_EmailRecipientsToString implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Expects an array with recipients for an email address as returned by Intrabuild_Filter_EmailRecipients.
     * Returns a comma separated string with the name values of this array, or the email address if no name value was
     * found.
     *
     * Input:
     * [
     *  ["ts@siteartwork.de", "Thorsten Suckow-Homberg"],
     *  ["yo@mtv.com"],
     *  ["pit@doggydog.com", "Pit Bull"],
     * ]

     *
     * Returns:
     * "Thorsten Suckow-Homberg, yo@mtv.com, Pit Bull"
     *
     *
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $parts = array();

        $pattern = '/[,"]/';

        foreach ($value as $address) {
            if (isset($address[1])) {
                $hit = preg_match($pattern, $address[1]);
                if ($hit != 0) {
                    $parts[] = '"' . $address[1] . '"';
                } else {
                    $parts[] = $address[1];
                }

                continue;
            }

            $parts[] = $address[0];
        }

        return implode(', ', $parts);
    }


}
