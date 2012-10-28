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
    protected $_delimiterLength;

    /**
     * Constructor.
     *
     * @param integer $strLen
     * @param integer $delimiter
     *
     * @throws Conjoon_Filter_Exception
     */
    public function __construct($strLen = 128, $delimiter = '...')
    {
        if (!$strLen || !$delimiter) {
            /**
             * @see Conjoon_Filter_Exception
             */
            require_once 'Conjoon/Filter/Exception.php';

            throw new Conjoon_Filter_Exception(
                "invalid arguments: \"$strLen\", \"$delimiter\""
            );
        }


        $this->_strLen          = $strLen;
        $this->_delimiter       = $delimiter;
        $this->_delimiterLength = strlen($delimiter);
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
        $delLen = $this->_delimiterLength;

        $firstDel = str_split($del);
        $firstDel = $firstDel[0];

        if (strlen($value) <= $strLen) {
            return $value;
        }

        $value = rtrim($value, $firstDel);

        $val = substr($value, 0, $strLen - $delLen);

        return $val . $del;
    }
}
