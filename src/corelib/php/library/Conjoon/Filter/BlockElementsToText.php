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