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
