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
