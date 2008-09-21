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
class Intrabuild_Filter_BlockElementsToText implements Zend_Filter_Interface
{
    private $_elements = array(
        'p', 'blockquote', 'hr', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'dl',
        'dt' ,'dd', 'ol', 'ul','li', 'table','tr', 'div', 'pre', 'address'
    );

    private $_search = array();

    public function __construct(Array $exclude = array())
    {
        if (!empty($exclude)) {
            $this->_elements = array_diff($this->_elements, $exclude);
        }

        foreach ($this->_elements as $element) {
            $this->_search[] = "/(<\/?)(".$element.")[^>]*>/i";
        }

    }

    /**
     * Replaces each block element start tag with a line break and removes the ending
     * tag of it.
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $value = preg_replace(
            $this->_search,
            "\n",
            $value
        );

        return $value;
    }
}