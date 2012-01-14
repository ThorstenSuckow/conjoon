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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @see Zend_Date
 */
require_once 'Zend/Date.php';

/**
 * Converts a UTC data to a local date.
 *
 * Note: This class will not check whether the date passed to "filter" is
 * actually a UTC date.
 *
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Filter_DateUtcToLocal implements Zend_Filter_Interface
{
    /**
     *@type string
     */
    const OPTIONS_TIMEZONE = 'timezone';

    /**
     * Stores thetimezone the UTC dates passed to filter() will be converted to.
     *
     * @type string
     */
    protected $_timezone;

    /**
     * Constructs a new instance of Conjoon_Filter_DateUtcToLocal
     *
     * @param Array|Zend_Config|null $options The argument holds
     * configuration options for this instance. If the argument is null, the
     * current timezone as found in date_default_timezone_get() will be used.
     * Otherwise, the key self::OPTIONS_TIMEZONE will be looked up and used
     * for converting the UTC date.
     *
     * @throws Conjoon\Filter\Exception If $options 'timezone' value is not
     * valid, if the option-key for the timezone is missing or if $options
     * is an invalid type
     * @see Conjoon\Filter\DateUtcToLocal::setTimezone()
     */
    public function __construct($options = null)
    {
        if (($options !== null &&
            !($options instanceof Zend_Config) && !is_array($options))
            || (is_array($options) && !array_key_exists(self::OPTIONS_TIMEZONE, $options)
            )
            ) {
            /**
             * @see Conjoon\Filter\Exception
             */
            require_once 'Conjoon/Filter/Exception.php';

            throw new Conjoon_Filter_Exception(
                "Invalid configuration for argument \"options\""
            );
        }

        if ($options === null) {
            $currentTimezone = date_default_timezone_get();

            $options = array(
                self::OPTIONS_TIMEZONE => $currentTimezone
            );
        }

        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        // check whether timezone is configured
        if (!array_key_exists(self::OPTIONS_TIMEZONE, $options)) {
            /**
             * @see Conjoon\Filter\Exception
             */
            require_once 'Conjoon/Filter/Exception.php';

            throw new Conjoon_Filter_Exception(
                "Missing configuration option for argument \"options\". "
                . "Key \"".self::OPTIONS_TIMEZONE."\" is missing"
            );
        }

        $this->setTimezone($options[self::OPTIONS_TIMEZONE]);
    }

    /**
     * Sets the timezone this filter will use to convert UTC dates to.
     *
     * @param string $timezone The timezone to use for converting UTC dates to.
     *
     * @return bool true if the timezone of this instance was successfully set
     * to the value of the passed argument
     *
     * @throws Conjoon_Filter_Exception if the passed argument is not a
     * valid timezone
     */
    public function setTimezone($timezone)
    {
        $currentTimezone    = date_default_timezone_get();
        $configuredTimezone = $timezone;

        // try to set the timezone here and fail if invalid
        $res = @date_default_timezone_set($configuredTimezone);

        if ($res === false) {
            date_default_timezone_set($currentTimezone);

            /**
             * @see Conjoon\Filter\Exception
             */
            require_once 'Conjoon/Filter/Exception.php';

            throw new Conjoon_Filter_Exception(
                "Timezone \"".$configuredTimezone."\" is invalid."
            );
        }
        date_default_timezone_set($currentTimezone);

        $this->_timezone = $configuredTimezone;

    }

    /**
     * Returns the timezone currently used with this instance.
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->_timezone;
    }

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
    public function filter($value)
    {
        $dt = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $d = strtotime($value);
        date_default_timezone_set($dt);

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




        $date->setTimezone($this->_timezone);
        return $date->get("YYYY-MM-dd HH:mm:ss");
    }
}
