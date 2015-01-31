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
class Conjoon_Filter_ShortenString implements Zend_Filter_Interface
{
    protected $_strLen;
    protected $_delimiter;
    protected $_delimiterLength;

    /**
     * Constructor.
     *
     * @param integer $strLen
     * @param integer $delimiter
     *
     * @throws Conjoon_Filter_Exception
     */
    public function __construct($strLen = 128, $delimiter = '...')
    {
        if (!$strLen || !$delimiter) {
            /**
             * @see Conjoon_Filter_Exception
             */
            require_once 'Conjoon/Filter/Exception.php';

            throw new Conjoon_Filter_Exception(
                "invalid arguments: \"$strLen\", \"$delimiter\""
            );
        }


        $this->_strLen          = $strLen;
        $this->_delimiter       = $delimiter;
        $this->_delimiterLength = strlen($delimiter);
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns a shortened version of the string based on the passed parameters
     * submitted to the constructor.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $strLen = $this->_strLen;
        $del    = $this->_delimiter;
        $delLen = $this->_delimiterLength;

        $firstDel = str_split($del);
        $firstDel = $firstDel[0];

        if (strlen($value) <= $strLen) {
            return $value;
        }

        $value = rtrim($value, $firstDel);

        $val = substr($value, 0, $strLen - $delLen);

        return $val . $del;
    }
}
