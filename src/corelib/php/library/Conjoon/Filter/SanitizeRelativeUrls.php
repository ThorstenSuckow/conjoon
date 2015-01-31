<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
 * Replaces relative links in tags by prepending the given
 * string.
 *
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_SanitizeRelativeUrls implements Zend_Filter_Interface
{
    protected $_link = "";

    protected $_valid = array();

    /**
     * Constructor.
     *
     * @param string $link The strink to prepend to relative urls
     * @param array $valid An array of strings which should not get prepended
     * with $link if found.
     *
     */
    public function __construct($link, Array $valid = array())
    {
        $this->_link  = $link;
        $this->_valid = $valid;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $link = rtrim($this->_link, '/').'/';

        $valid = implode('|', $this->_valid);

        $value = preg_replace(
            array(
                ',<a([^>]+)href="(?!https?://|ftp://|mailto:|news:'.($valid ? '|' . $valid : '').')([^>"\s]+)",i',
                ',<img([^>]+)src="(?!https?://|ftp://|mailto:|news:'.($valid ? '|' . $valid : '').')([^>"\s]+)",i',
                ',<a([^>]+)href=\'(?!https?://|ftp://|mailto:|news:'.($valid ? '|' . $valid : '').')([^>\'\s]+)\',i',
                ',<img([^>]+)src=\'(?!https?://|ftp://|mailto:|news:'.($valid ? '|' . $valid : '').')([^>\'\s]+)\',i'
            ),
            array(
                '<a\1href="'.$link.'\2"',
                '<img\1src="'.$link.'\2"',
                '<a\1href=\''.$link.'\2\'',
                '<img\1src=\''.$link.'\2\''
            ),
            $value
        );

        return $value;
    }
}
