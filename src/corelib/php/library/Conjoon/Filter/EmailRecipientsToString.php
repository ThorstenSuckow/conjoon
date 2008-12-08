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
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Filter_EmailRecipientsToString implements Zend_Filter_Interface
{
    private $_useQuoting = true;

    /**
     * Constructor.
     *
     */
    public function __construct($useQuoting = true)
    {
        $this->_useQuoting = $useQuoting;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Expects an array with recipients for an email address as returned by Conjoon_Filter_EmailRecipients.
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

        $pattern = '/[,@\[\];"]/';

        foreach ($value as $address) {
            if (isset($address[1])) {

                $hit = $this->_useQuoting ? preg_match($pattern, $address[1]) : 0;

                if ($hit != 0) {
                    // quote only if the string is not already quoted
                    if  (substr($address[1], 0, 1) != '"' || substr($address[1], -1) != '"') {
                        // if the string needs quoting, check if the quotes within the string
                        // are already escaped
                        if (strpos(trim($address[1], '"'), '\"') === false && strpos(trim($address[1], '"'), '"') !== false) {
                            $address[1] = str_replace('"', '\"', $address[1]);
                        }
                        $parts[] = '"' . $address[1] . '"';
                    } else {
                        $parts[] = $address[1];
                    }
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
