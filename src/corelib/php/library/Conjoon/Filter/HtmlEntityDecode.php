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
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Filter_HtmlEntityDecode implements Zend_Filter_Interface
{
    protected $_quoteStyle = ENT_COMPAT;
    protected $_charset    = 'ISO-8859-1';


    /**
     * Constructor.
     *
     * @param mixed $quoteStyle
     * @param string $charset
     *
     */
    public function __construct($quoteStyle = ENT_COMPAT, $charset = 'ISO-8859-1')
    {
        $this->_quoteStyle = $quoteStyle;
        $this->_charset    = $charset;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the html entity decoded string.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        return html_entity_decode((string)$value, $this->_quoteStyle, $this->_charset);
    }
}
