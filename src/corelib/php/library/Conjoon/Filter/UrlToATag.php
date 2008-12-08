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
class Conjoon_Filter_UrlToATag implements Zend_Filter_Interface
{
    private $_attributes = array();

    private $_attributeString = "";

    /**
     * Constructor.
     *
     * @param array $attributes an optional set of attributes to attach to each
     * a-tag.
     *
     */
    public function __construct(Array $attributes = array())
    {
        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $this->_attributeString = $key . '="'.$value.'" ';
            }
        }
    }


    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the text with all patterns that look like a hyperlink or an address
     * in the www replaced by a <a href="{pattern}">{pattern}</a>.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $value = preg_replace(
            //                   1                  2        3           4          5
            "/(?:(ftp:\/\/|https:\/\/|http:\/\/)|(www\.))(\S+\b\/?)([ [:punct:]]*)(\s|$)/ie",
            "'<a ".$this->_attributeString." href=\"'.('\\1' ? '\\1' : 'http://').'$2$3\">$1$2$3</a>$4$5'",
            $value
        );

        return $value;
    }
}