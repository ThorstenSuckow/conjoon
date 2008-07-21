<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: PasswordMask.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Filter/PasswordMask.php $
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
class Intrabuild_Filter_ShortenString implements Zend_Filter_Interface
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
