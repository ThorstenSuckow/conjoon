<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_ShortenString implements Zend_Filter_Interface
{
    protected $_strLen;
    protected $_delimiter;

    /**
     * Constructor.
     *
     * @param integer $strLen
     * @param integer $delimiter
     *
     */
    public function __construct($strLen = 128, $delimiter = '...')
    {
        $this->_strLen    = $strLen;
        $this->_delimiter = $delimiter;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns a shortened version of the string based on the passed parameters
     * submitted to the constructor.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $strLen = $this->_strLen;
        $del    = $this->_delimiter;

        if (strlen($value) <= $strLen) {
            return $value;
        }

        $val = substr($value, 0, $strLen);

        if ($del == '...' && substr($val, -1) == '.') {
            return $val . '..';
        } else {
            return $val . $del;
        }
    }
}
