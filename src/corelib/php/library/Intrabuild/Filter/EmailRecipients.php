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
class Intrabuild_Filter_EmailRecipients implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Expects an array with recipients for an email address. Returns an array
     * with address/name pairs.
     *
     * Input:
     * [
     *  "\"Thorsten Suckow-Homberg\" <ts@siteartwork.de>, yo@mtv.com",
     *  "\"Pit Bull\" <pit@doggydog.com>",
     * ]
     *
     * Returns:
     * [
     *  ["ts@siteartwork.de", "Thorsten Suckow-Homberg"],
     *  ["yo@mtv.com"],
     *  ["pit@doggydog.com", "Pit Bull"],
     * ]
     *
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $value = (array)$value;

        $addr = array();
        for ($i = 0, $len = count($value); $i < $len; $i++) {
            $parts = explode(',', $value[$i]);

            for ($a = 0, $lena = count($parts); $a < $lena; $a++) {
                $rep = $this->_extractRecipient(trim($parts[$a]));
                if (!empty($rep)) {
                    $addr[] = $rep;
                }
            }
        }

        return $addr;
    }

    private function _extractRecipient($address)
    {
        $parts = explode("<", $address);

        if (count($parts) == 1) {
            if ($parts[0] == "") {
                return array();
            } else {
                return array($parts[0]);
            }
        } else {
            $email = trim(str_replace(">", "", $parts[1]));
            $name  = trim(str_replace(
                array("\"", "'"),
                "",
                $parts[0]
            ));

            if ($email == "") {
                return array();
            }

            if ($name == "") {
                return array($email);
            }

            return array($email, $name);
        }

        return array();
    }
}
