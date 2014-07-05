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
 * Filter for retrieving UTC datetime values in the format
 * YYYY-MM-dd HH:mm:ss.
 *
 * The method will try to guess the timezone of the string as passed
 * to filter(), and afterwards return it as its UTC equivalent.
 * If your looking for this class pendant, refer to
 * Conjoon\Filter\DateUtcToLocal, which will convert a UTC date to any
 * valid timezone.
 *
 * @category   Filter
 * @package    Conjoon\Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_DateToUtc implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the UTC-date time format for the specified value.
     * Will return the date for the 1970-1-1 if the passed argument
     * was not in a valid date-format.
     *
     * @param  mixed $value
     * @return string
     *
     * @throws Conjoon_Filter_Exception If Zend_Date could not handle the passed
     * argument. Note, that almost every time Zend_Date will try to convert
     * the passed argument to a date object.
     *
     * @deprecated use Conjoon_Date_Format::toUtc
     */
    public function filter($value)
    {
        /**
         * @see Conjoon_Date_Format
         */
        require_once 'Conjoon/Date/Format.php';

        try{
            return Conjoon_Date_Format::toUtc($value);
        } catch (Conjoon_Date_Exception $e) {
            /**
             * @see Conjoon_Filter_Exception
             */
            require_once 'Conjoon/Filter/Exception.php';

            throw new Conjoon_Filter_Exception($e->getMessage());
        }
    }
}
