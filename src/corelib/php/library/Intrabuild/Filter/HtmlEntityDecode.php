<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Nl2br.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Filter/Nl2br.php $
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
class Intrabuild_Filter_HtmlEntityDecode implements Zend_Filter_Interface
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
