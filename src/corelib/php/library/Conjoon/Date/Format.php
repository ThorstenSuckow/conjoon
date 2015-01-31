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
 * Date-format utility methods.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Date_Format {


    /**
     * Converts the passed string to a date in the timezone of self::$_timezone.
     *
     * Returns a UTC date converted to the local date as determined by the
     * timezonefound in self::$_timezone, which can be specified when
     * instantiating this class. The date sttring returned will be in the format
     * of YYYY-MM-dd HH:mm:ss.
     * Note: The passed argument must already be a UTC date! This method will
     * not check whether the passed argument's timezone is UTC
     * This method will gracefully fall back to teh default date of
     * 1970-01-01 00:00:00 if it could not convert the passed argument properly.
     *
     * @param  mixed $value
     * @return string
     */
    public static function utcToLocal($value, $targetTimezone = null)
    {
        if ($targetTimezone === null) {
            $targetTimezone = date_default_timezone_get();
        }

        //test first if target timezone is valid
        $currentTimezone    = date_default_timezone_get();
        $configuredTimezone = $targetTimezone;

        // try to set the timezone here and fail if invalid
        $res = @date_default_timezone_set($configuredTimezone);

        if ($res === false) {
            date_default_timezone_set($currentTimezone);

            /**
             * @see Conjoon\Date\Exception
             */
            require_once 'Conjoon/Date/Exception.php';

            throw new Conjoon_Date_Exception(
                "Timezone \"".$configuredTimezone."\" is invalid."
            );
        }
        date_default_timezone_set($currentTimezone);

        $timezoneToUse = $configuredTimezone;



        $dt = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $d = strtotime($value);
        date_default_timezone_set($dt);

        /**
         * @see Zend_Date
         */
        require_once 'Zend/Date.php';

        if ($d === false) {
            try {
                $date = new Zend_Date();
                $date->setTimezone('UTC');
                $date->set($value);
            } catch (Zend_Date_Exception $e) {
                $date = new Zend_Date("1970-01-01 00:00:00");
            }
        } else {
            try {
                $date = new Zend_Date();
                $date->setTimezone('UTC');
                $date->set($d);
            } catch (Zend_Date_Exception $e) {
                $date = new Zend_Date("1970-01-01 00:00:00");
            }
        }

        $date->setTimezone($timezoneToUse);
        return $date->get("YYYY-MM-dd HH:mm:ss");
    }

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
     * Returns the UTC-date time format for the specified value.
     * Will return the date for the 1970-1-1 if the passed argument
     * was not in a valid date-format.
     *
     * @param  mixed $value
     * @return string
     *
     * @throws Conjoon_Date_Exception If Zend_Date could not handle the passed
     * argument. Note, that almost every time Zend_Date will try to convert
     * the passed argument to a date object.
     */
    public static function toUtc($value)
    {
        // we need to set the default timezone here since strtotime
        // works with the timezone as returned by
        // date_default_timezone_get()
        $t = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $d = strtotime($value);
        date_default_timezone_set($t);

        /**
         * @see Zend_Date
         */
        require_once 'Zend/Date.php';

        if ($d === false) {
            try {
                $date = new Zend_Date($value);
            } catch (Zend_Date_Exception $e) {
                /**
                 * @see Conjoon_Date_Exception
                 */
                require_once 'Conjoon/Date/Exception.php';

                throw new Conjoon_Date_Exception(
                    "Problem with value for date \"$value\" - Zend_Date threw an"
                        . " exception with the following message: "
                        . $e->getMessage()
                );
            }
        } else {
            try {
                $date = new Zend_Date($d);
            } catch (Zend_Date_Exception $e) {
                /**
                 * @see Conjoon_Date_Exception
                 */
                require_once 'Conjoon/Date/Exception.php';

                throw new Conjoon_Date_Exception(
                    "Problem with value for date \"$value\" - Zend_Date threw an"
                        . " exception with the following message: "
                        . $e->getMessage()
                );
            }
        }

        $date->setTimezone('UTC');
        return $date->get('YYYY-MM-dd HH:mm:ss');
    }


}