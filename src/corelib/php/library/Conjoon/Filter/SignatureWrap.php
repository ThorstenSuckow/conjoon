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
class Conjoon_Filter_SignatureWrap implements Zend_Filter_Interface
{
    private $_start = '';

    private $_end = '';

    /**
     * Constructor.
     *
     * @param string $start The token to prepend signature beginning
     * @param string $end The token to append at signature end
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
     * Returns the text trimmed along with a signature found wrapped in the
     * specified $_start/$_end token.
     *
     * <pre>
     * Text
     *
     * --
     * Signature
     * [\n]
     * [\n]
     * [\n]
     * </pre>
     *
     * becomes
     *
     * <pre>
     * Text
     *
     * $this->_start
     * --
     * Signature
     * $this->_end
     * </pre>
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $index = strpos($value, "\n-- \n");

        if ($index === false) {
            return $value;
        }

        if ($index === 0) {
            return $this->_start . rtrim($value) . $this->_end;
        }

        return substr($value, 0, $index+1)
               . $this->_start
               . trim(substr($value, $index+1))
               . $this->_end;
    }
}