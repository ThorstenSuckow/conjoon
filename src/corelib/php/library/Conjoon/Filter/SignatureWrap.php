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
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_SignatureWrap implements Zend_Filter_Interface
{
    private $_start = '';

    private $_end = '';

    /**
     * Constructor.
     *
     * @param string $start The token to prepend signature beginning
     * @param string $end The token to append at signature end
     *
     */
    public function __construct($start = '', $end = '')
    {
        $this->_start = $start;
        $this->_end   = $end;
    }


    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the text trimmed along with a signature found wrapped in the
     * specified $_start/$_end token.
     *
     * <pre>
     * Text
     *
     * --
     * Signature
     * [\n]
     * [\n]
     * [\n]
     * </pre>
     *
     * becomes
     *
     * <pre>
     * Text
     *
     * $this->_start
     * --
     * Signature
     * $this->_end
     * </pre>
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $index = strpos($value, "\n-- \n");

        if ($index === false) {
            return $value;
        }

        if ($index === 0) {
            return $this->_start . rtrim($value) . $this->_end;
        }

        return substr($value, 0, $index+1)
               . $this->_start
               . trim(substr($value, $index+1))
               . $this->_end;
    }
}