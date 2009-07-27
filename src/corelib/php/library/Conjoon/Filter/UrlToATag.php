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
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        // dev note: the regex is a little bit clumsy: it first extracts all contents in between
        // "a"-tags and "img"-tags and assigns a placeholder, the processes the regex for the urls
        // and remaps the placeholders with their cached values
        // RegEx experts, feel free to contribute a more elegant solution!

        // this array maps all replaced elements
        $replaceMap = array();
        $mk          = time();

        // replace all a tags
        $pattern = "/(<(a)\b[^>]*>)+(.*?)(<\/\\2>)+/is";
        $matches = array();
        preg_match_all($pattern, $value, $matches, PREG_SET_ORDER);
        for ($i = 0, $len = count($matches); $i < $len; $i++) {
            $value = str_replace($matches[$i][0], '@a@'.$mk.'_'.$i.'@a@', $value);
            $replaceMap[] = array($matches[$i][0], '@a@'.$mk.'_'.$i.'@a@');
        }

        // replace all img tags
        $pattern = "/(<img\b[^>]*\/>)/is";
        $matches = array();
        preg_match_all($pattern, $value, $matches, PREG_SET_ORDER);
        for ($i = 0, $len = count($matches); $i < $len; $i++) {
            $value = str_replace($matches[$i][0], '@img@'.$mk.'_'.$i.'@img@', $value);
            $replaceMap[] = array($matches[$i][0], '@img@'.$mk.'_'.$i.'@img@');
        }

        // now work on remaining links
        $pattern = "/(?:(ftp:\/\/|https:\/\/|http:\/\/)|(www\.))([a-zA-Z0-9-:\.\/\_\?\%\#\&\=\;\~\!\(\)]+)/ie";
        $value = preg_replace(
            $pattern,
            "'<a ".$this->_attributeString." href=\"'.('\\1' ? '\\1' : 'http://').'$2$3\">$1$2$3</a>'",
            $value
        );

        // reassign replacements
        for ($i = 0, $len = count($replaceMap); $i < $len; $i++) {
            $value = str_replace($replaceMap[$i][1], $replaceMap[$i][0], $value);
        }

        return $value;
    }

}