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
class Intrabuild_Filter_StringPrependIf implements Zend_Filter_Interface
{
    private $_startsWith = "";

    /**
     * Constructor.
     *
     * @param string $startsWith The string to prepend to the filtered value
     * if and only if it does not start with this string already.
     */
    public function __construct($startsWith = "")
    {
        $this->_startsWith = $startsWith;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Prepends the string with the given value if and only if it does not start with it already .
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        if ($this->_startsWith === "") {
            return $value;
        }

        $value = ltrim((string)($value));

        $index = strpos($value, $this->_startsWith);

        if ($index === 0) {
            return $value;
        }

        return $this->_startsWith . $value;
    }
}