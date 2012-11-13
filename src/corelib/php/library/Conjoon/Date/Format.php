<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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


}