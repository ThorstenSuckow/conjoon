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
class Intrabuild_Filter_UrlToATag implements Zend_Filter_Interface
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