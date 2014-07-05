<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
        // replace all non XHTML img tags
        $pattern = "/(<(img)\b[^>]*>)+(.*?)(<\/\\2>)+/is";
        $matches = array();
        preg_match_all($pattern, $value, $matches, PREG_SET_ORDER);
        for ($i = 0, $len = count($matches); $i < $len; $i++) {
            $value = str_replace($matches[$i][0], '@img@'.$mk.'_'.$i.'@img@', $value);
            $replaceMap[] = array($matches[$i][0], '@img@'.$mk.'_'.$i.'@img@');
        }

        // replace all XHTML img tags
        $pattern = "/(<img\b[^>]*\/>)/is";
        $matches = array();
        preg_match_all($pattern, $value, $matches, PREG_SET_ORDER);
        for ($i = 0, $len = count($matches); $i < $len; $i++) {
            $value = str_replace($matches[$i][0], '@img1@'.$mk.'_'.$i.'@img1@', $value);
            $replaceMap[] = array($matches[$i][0], '@img1@'.$mk.'_'.$i.'@img1@');
        }

        // replace all non conform XHTML img tags
        $pattern = "/(<img\b[^>]*\>)/is";
        $matches = array();
        preg_match_all($pattern, $value, $matches, PREG_SET_ORDER);
        for ($i = 0, $len = count($matches); $i < $len; $i++) {
            $value = str_replace($matches[$i][0], '@img2@'.$mk.'_'.$i.'@img2@', $value);
            $replaceMap[] = array($matches[$i][0], '@img2@'.$mk.'_'.$i.'@img2@');
        }

        // now work on remaining links
        $pattern = "/(?:(ftp:\/\/|https:\/\/|http:\/\/)|(www\.))([a-zA-Z0-9-:\.\/\_\?\%\#\&\=\;\~\!\+]+)/i";
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