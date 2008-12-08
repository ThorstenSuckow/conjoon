<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
class Conjoon_Filter_BlockElementsToText implements Zend_Filter_Interface
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