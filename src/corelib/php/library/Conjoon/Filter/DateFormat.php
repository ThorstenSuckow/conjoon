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
 * @see Zend_Date
 */
require_once 'Zend/Date.php';

/**
 * @see Zend_Locale_Format
 */
require_once 'Zend/Locale/Format.php';

/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_DateFormat implements Zend_Filter_Interface
{

    /**
     * @var string
     */
    private $_format = "F j, Y, g:i a";

    /**
     * @var string
     */
    private $_inputFormat = null;

    public function __construct($format = null, $inputFormat = null)
    {
        if ($format != null) {
            $this->_format = $format;
        }

        if ($inputFormat != null) {
            $this->_inputFormat = $inputFormat;
        }
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the date formatted for the format defined with the constructor.
     * Will return the date for the 1970-1-1 if the passed argument
     * was not in a valid date-format.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        // get Zend_Date options
        $dateOptions = Zend_Date::setOptions();

        // set format type to iso
        Zend_Date::setOptions(array('format_type' => 'iso'));

        $inputFormat = $this->_inputFormat;
        $format      = $this->_format;

        if ($inputFormat) {
            $inputFormat = Zend_Locale_Format::convertPhpToIsoFormat(
                $inputFormat
            );
        }

        $format = Zend_Locale_Format::convertPhpToIsoFormat($format);

        $d = $value;

        if (!is_numeric($value)) {
            $d = strtotime($value);
        }

        if ($d === false) {
            try {
                $date = new Zend_Date($value, $inputFormat);
            } catch (Zend_Date_Exception $e) {
                $date = new Zend_Date("1970-01-01 00:00:00");
            }
        } else {
            try {
                $date = new Zend_Date($d, Zend_Date::TIMESTAMP);
            } catch (Zend_Date_Exception $e) {
                $date = new Zend_Date("1970-01-01 00:00:00");
            }
        }

        $d = $date->get($format);

        Zend_Date::setOptions(array('format_type' => $dateOptions['format_type']));

        return $d;
    }
}
