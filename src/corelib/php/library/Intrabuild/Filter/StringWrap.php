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
class Intrabuild_Filter_StringWrap implements Zend_Filter_Interface
{
    private $_start = '';

    private $_end = '';

    /**
     * Constructor.
     *
     * @param string $start The token to prepend to the string
     * @param string $end The token to append to the string
     *
     */
    public function __construct($start = '', $end = '')
    {
        $this->_start = $start;
        $this->_end   = $end;
    }


    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the text wrapped in $_start and $_end.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        return $this->_start . (string)$value . $this->_end;
    }

}