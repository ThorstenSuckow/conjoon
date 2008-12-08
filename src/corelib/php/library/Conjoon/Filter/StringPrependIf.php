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
class Conjoon_Filter_StringPrependIf implements Zend_Filter_Interface
{
    private $_startsWith = array();

    private $_prependWith = "";

    /**
     * Constructor.
     *
     * @param array $startsWith An array with strings to check if they occur
     * at the start of the string to filter.
     * @param string $prependWith The string to prepend the filtered string with
     * if none of the strings in $startsWith where found
     *
     */
    public function __construct($startsWith = array(), $prependWith = "")
    {
        $this->_startsWith  = $startsWith;
        $this->_prependWith = $prependWith;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Prepends the string with the given value if and only if it does not start with any
     * strings found in $startsWith.
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        $orgValue = $value;

        if ($this->_prependWith === "") {
            return $orgValue;
        }

        $value = ltrim((string)$value);

        // special case: trimmed strings are equal
        if (trim($orgValue) == trim($this->_prependWith)) {
            return $orgValue;
        }

        if (empty($this->_startsWith)) {
            return $this->_prependWith . $value;
        }

        $found = false;
        foreach ($this->_startsWith as $sub) {
            if (strpos($value, $sub) === 0) {
                $found = true;
                break;
            }
        }

        if ($found) {
            return $orgValue;
        }

        return $this->_prependWith . $value;
    }
}