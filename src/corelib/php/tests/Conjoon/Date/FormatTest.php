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
 * @see Conjoon_Date_Format
 */
require_once 'Conjoon/Date/Format.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Date_FormatTest extends PHPUnit_Framework_TestCase {

    protected $_timezones = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_timezones = array(
            'valid'   => 'Europe/Berlin',
            'invalid' => 'mEurope/Berlin'
        );
    }

    /**
     *
     * @return void
     */
    public function tearDown()
    {
    }

    /**
     * Ensures everything works as expected
     *
     * @expectedException Conjoon_Date_Exception
     */
    public function testUtcToLocalInvalidTimezoneException()
    {
        Conjoon_Date_Format::utcToLocal(
            "1970-01-01 00:00:00",
            $this->_timezones['invalid']
        );
    }

    /**
     * Ensures everything works as expected.
     *
     */
    public function testUtcToLocal()
    {
        $currt = date_default_timezone_get();

        $asserts = array(
            "Antarctica/Casey" => array(
                "1978-12-13 20:45:54" => "1978-12-14 04:45:54"
            ),
            "Asia/Dhaka" => array(
                "2038-01-19 03:14:07" => "2038-01-19 09:14:07"
            ),
            "Indian/Maldives" => array(
                "2011-11-02 07:17:22" => "2011-11-02 12:17:22"
            ),
            "Atlantic/South_Georgia" => array(
                "2011-11-04 09:24:21" => "2011-11-04 07:24:21"
            ),
            "Europe/Oslo" => array(
                "2011-11-01 15:20:06" => "2011-11-01 16:20:06"
            ),
            "Europe/Amsterdam" => array(
                "2011-11-01 15:00:16" => "2011-11-01 16:00:16"
            ),
            "Europe/London" => array(
                "2011-06-20 14:09:38" => "2011-06-20 15:09:38"
            ),
            "America/Santarem" => array(
                "2010-02-01 00:00:00" => "2010-01-31 21:00:00"
            ),
            "America/Swift_Current" => array(
                "2010-02-01 00:00:00" => "2010-01-31 18:00:00"
            ),
            "Australia/Yancowinna" => array(
                "2010-02-01 08:00:00" => "2010-02-01 18:30:00"
            )
        );

        foreach ($asserts as $timezone => $values) {
            foreach ($values as $input => $output) {
                $this->assertEquals(
                    $output,
                    Conjoon_Date_Format::utcToLocal($input, $timezone)
                );
            }
        }

        $this->assertEquals($currt, date_default_timezone_get());

    }

    /**
     * Ensures that default time is returned for invalid time value.
     *
     */
    public function testUtcToLocalDefaultTimeForInvalidArgument()
    {
        $this->assertEquals(
            "1970-01-01 00:00:00", Conjoon_Date_Format::utcToLocal("")
        );

        $this->assertEquals(
            "1970-01-01 00:00:00", Conjoon_Date_Format::utcToLocal("bla")
        );
    }
}
