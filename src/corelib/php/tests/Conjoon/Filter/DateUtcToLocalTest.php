<?php
/**
 * conjoon
 * (c) 2002-2011 siteartwork.de/conjoon.org
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
 * @see Conjoon_Filter_DateUtcToLocal
 */
require_once 'Conjoon/Filter/DateUtcToLocal.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_DateUtcToLocalTest extends PHPUnit_Framework_TestCase {

    /**
     * Conjoon_Filter_DateUtcToLocal object
     *
     * @var Conjoon_Filter_DateUtcToLocal
     */
    protected $_filter;

    protected $_validConfigArray;

    protected $_validConfigArrayWrongTimezone;

    protected $_inValidConfigArray;

    /**
     * Creates a new Conjoon_Filter_DateUtcToLocal object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Conjoon_Filter_DateUtcToLocal();

        $this->_validConfigArray = array(
            Conjoon_Filter_DateUtcToLocal::OPTIONS_TIMEZONE => 'Europe/Berlin'
        );

        $this->_inValidConfigArray = array(
            Conjoon_Filter_DateUtcToLocal::OPTIONS_TIMEZONE.'m' => 'Europe/Berlin'
        );

        $this->_validConfigArrayWrongTimezone = array(
            Conjoon_Filter_DateUtcToLocal::OPTIONS_TIMEZONE.'m' => 'dEurope/Berlin'
        );

    }

    /**
     * Ensures that the exception derives from Zend_Filter_Interface
     *
     * @return void
     */
    public function testInterface()
    {
        $this->assertTrue(
            $this->_filter instanceof Zend_Filter_Interface
        );
    }

    public function testConstructorWithZendConfigSuccess()
    {
        /**
         * @see Zend_Config
         */
        require_once 'Zend/Config.php';

        $config = new Zend_Config($this->_validConfigArray);

        $filter = new Conjoon_Filter_DateUtcToLocal($config);

        $this->assertTrue($filter instanceof Conjoon_Filter_DateUtcToLocal);
    }

    public function testConstructorWithArraySuccess()
    {
        $filter = new Conjoon_Filter_DateUtcToLocal($this->_validConfigArray);

        $this->assertTrue($filter instanceof Conjoon_Filter_DateUtcToLocal);
    }

    public function testConstructorWithNull()
    {
        $dt = date_default_timezone_get();

        $filter = new Conjoon_Filter_DateUtcToLocal();

        $this->assertEquals($dt, $filter->getTimezone());
    }

    /**
     *
     * @expectedException Conjoon_Filter_Exception
     */
    public function testConstructorWithInvalidArgument()
    {
        $filter = new Conjoon_Filter_DateUtcToLocal("blah.");
    }

    /**
     *
     * @expectedException Conjoon_Filter_Exception
     */
    public function testConstructorWithInvalidArray()
    {
        $filter = new Conjoon_Filter_DateUtcToLocal($this->_inValidConfigArray);
    }

    /**
     *
     * @expectedException Conjoon_Filter_Exception
     */
    public function testConstructorWithInvalidZendConfig()
    {
        /**
         * @see Zend_Config
         */
        require_once 'Zend/Config.php';

        $filter = new Conjoon_Filter_DateUtcToLocal(
            new Zend_Config($this->_inValidConfigArray)
        );
    }

    /**
     *
     * @expectedException Conjoon_Filter_Exception
     */
    public function testConstructorWithValidArrayButInvalidTimezone()
    {
        $filter = new Conjoon_Filter_DateUtcToLocal(
            $this->_validConfigArrayWrongTimezone
        );
    }

    /**
     *
     * @expectedException Conjoon_Filter_Exception
     */
    public function testConstructorWithValidZendConfigButInvalidTimezone()
    {
        /**
         * @see Zend_Config
         */
        require_once 'Zend/Config.php';

        $filter = new Conjoon_Filter_DateUtcToLocal(
            new Zend_Config($this->_validConfigArrayWrongTimezone)
        );
    }

    /**
     *
     */
    public function testGetTimezone()
    {
        $dt = date_default_timezone_get();

        $this->assertEquals($dt, $this->_filter->getTimezone());
    }

    /**
     *
     */
    public function testSetTimezone()
    {
        $this->_filter->setTimezone("America/Los_Angeles");

        $this->assertEquals("America/Los_Angeles", $this->_filter->getTimezone());
    }

    /**
     * Ensures everything works as expected
     *
     * @return void
     */
    public function testFilter()
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
            $filter = new Conjoon_Filter_DateUtcToLocal(array(
                Conjoon_Filter_DateUtcToLocal::OPTIONS_TIMEZONE => $timezone
            ));

            foreach ($values as $input => $output) {
                $this->assertEquals($output, $filter->filter($input));
            }
        }

        $this->assertEquals($currt, date_default_timezone_get());
    }

    /**
     * Ensures that no exception exception is thrown if invalid argument
     * is passed, and default value returned instead
     *
     * @return void
     */
    public function testDefaultValueWillBeReturnedForInvalidValue()
    {
        $this->assertEquals(
            "1970-01-01 00:00:00", $this->_filter->filter("")
        );
    }

}
