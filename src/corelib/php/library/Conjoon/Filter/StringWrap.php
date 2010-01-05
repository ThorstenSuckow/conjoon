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
class Conjoon_Filter_StringWrap implements Zend_Filter_Interface
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