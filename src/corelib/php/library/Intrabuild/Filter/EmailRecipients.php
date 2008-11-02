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
    private $_addslashes;
    private $_useQuoting;

    /**
     * Constructor.
     *
     */
    public function __construct($addSlashes = true, $useQuoting = true)
    {
        $this->_addSlashes = $addSlashes;
        $this->_useQuoting = $useQuoting;
    }

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

        $pattern = '/(^|\s|,)*+(([^,"]*".*?"[^,"]*)\s*<(.*?)>|([^",]+)\s*<(.*?)>|([^,\s"<>]+))[,\s$]?/msi';

        for ($i = 0, $len = count($value); $i < $len; $i++) {

            // normalize the string - replace linebreaks, tabs and leading/trailing whitespace/commas
            $value[$i] = preg_replace(
                "/^[\s,]+|[\s,]+$|[\t\r\n]/",
                '',
                $value[$i]
            );

            if (trim($value[$i]) == "") {
                continue;
            }

            preg_match_all($pattern, $value[$i], $matches, PREG_SET_ORDER);

            $i = 0;
            foreach ($matches as $match) {
                if (isset($match[7])) {
                    $addr[$i] = array(trim($match[7]));
                } else if (isset($match[6])) {
                    $addr[$i] = array(trim($match[6]), trim($match[5]));
                } else {
                    $addr[$i] = array(trim($match[4]), trim($match[3]));
                }

                if ($this->_addSlashes === true && isset($addr[$i][1])) {
                    // assume the name is quoted, add quotes to the whole name
                    // i.e. 'Thorsten \"Suckow-Homberg\"' becomes '"Thorsten \"Suckow-Homberg\""'
                    if (strpos($addr[$i][1], "\\\"") !== false) {
                        $addr[$i][1] = '"' . $addr[$i][1] . '"';
                    } else if (strpos(trim($addr[$i][1], '"'), '"') !== false) {
                        // assume the name is quoted, without escaping
                        // i.e. 'Thorsten "Suckow-Homberg"' becomes '"Thorsten \"Suckow-Homberg\""'
                        $addr[$i][1] = '"' . addslashes($addr[$i][1]) . '"';
                    } else if (preg_match('/[,@\[\];"]/', trim($addr[$i][1], '"')) === 0) {
                        // we want only the name!!! If it does not need to be quoted, don't quote it!
                        $addr[$i][1] = trim($addr[$i][1], '"');
                    }

                } else if ($this->_addSlashes === false && isset($addr[$i][1])) {
                    // asumme the string is quoted since escaped quotes are found
                    // the filter assumes that escaped quotes only occure if and only
                    // if the whoile string is quoted
                    if (strpos($addr[$i][1], "\\\"") !== false) {
                        $temp = stripslashes($addr[$i][1]);
                        if (strpos($temp, '"') === 0) {
                            $temp = substr($temp, 1);
                        }
                        if (strrpos($temp, '"') === strlen($temp)-1) {
                            $temp = substr($temp, 0, -1);
                        }

                        $addr[$i][1] = $temp;
                    } else if (strpos($addr[$i][1], '"') !== false) {
                        // assume the string is quoted and can be unquoted safely
                        // since not escaped quotes occure, except if any of the following strings
                        // can be found: @,
                        if ($this->_useQuoting === false || preg_match('/[,@\[\];]/', $addr[$i][1]) == 0) {
                            $addr[$i][1] = trim($addr[$i][1], '"');
                        }
                    }
                }

                $i++;
            }
        }

        return $addr;
    }


}
