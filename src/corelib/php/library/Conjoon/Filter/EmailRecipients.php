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
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Filter_EmailRecipients implements Zend_Filter_Interface
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

        $a = 0;
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

            foreach ($matches as $match) {
                if (isset($match[7])) {
                    $addr[$a] = array(trim($match[7]));
                } else if (isset($match[6])) {
                    $addr[$a] = array(trim($match[6]), trim($match[5]));
                } else {
                    $addr[$a] = array(trim($match[4]), trim($match[3]));
                }

                if ($this->_addSlashes === true && isset($addr[$a][1])) {
                    $temp = $addr[$a][1];

                    // assume the name is quoted, add quotes to the whole name
                    // i.e. 'Thorsten \"Suckow-Homberg\"' becomes '"Thorsten \"Suckow-Homberg\""'
                    // quote only if it's not already quoted!
                    if (strpos($temp, '\"') !== false) {
                        if  (substr($temp, 0, 1) != '"' || substr($temp, -1) != '"') {
                            $addr[$a][1] = '"' . $temp . '"';
                        }
                    } else if (strpos(trim($temp, '"'), '"') !== false) {
                        // assume the name is quoted, without escaping
                        // i.e. 'Thorsten "Suckow-Homberg"' becomes '"Thorsten \"Suckow-Homberg\""'
                        $addr[$a][1] = '"' . addslashes($temp) . '"';
                    } else if (preg_match('/[,@\[\];"]/', trim($temp, '"')) === 0) {
                        // we want only the name!!! If it does not need to be quoted, don't quote it!
                        $addr[$a][1] = trim($temp, '"');
                    }

                } else if ($this->_addSlashes === false && isset($addr[$a][1])) {
                    // asumme the string is quoted since escaped quotes are found
                    // the filter assumes that escaped quotes only occure if and only
                    // if the whole string is quoted
                    if (strpos($addr[$a][1], '\"') !== false) {
                        $temp = stripslashes($addr[$a][1]);

                        if (strpos($temp, '"') === 0) {
                            $temp = substr($temp, 1);
                        }
                        if (strrpos($temp, '"') === strlen($temp)-1) {
                            $temp = substr($temp, 0, -1);
                        }

                        // find the first quote, check if there is anything that definitely
                        // needs to be quoted
                        if ($this->_useQuoting !== false) {
                            $t2 = substr($temp, 0, strpos($temp, '"'));
                            $t3 = substr($temp, strrpos($temp, '"'));

                            if (preg_match('/[,@\[\];]/', $t2) !== 0 || preg_match('/[,@\[\];]/', $t3) !== 0) {
                                //leave anything as is
                                $temp = $addr[$a][1];
                            }
                        }

                        $addr[$a][1] = $temp;

                    } else if (strpos($addr[$a][1], '"') !== false) {
                        // assume the string is quoted and can be unquoted safely
                        // since not escaped quotes occure, except if any of the following chars
                        // can be found: [,@\[\];]
                        if ($this->_useQuoting === false || preg_match('/[,@\[\];]/', $addr[$a][1]) == 0) {
                            $addr[$a][1] = trim($addr[$a][1], '"');
                        }
                    }
                }

                $a++;
            }
        }

        return $addr;
    }


}
