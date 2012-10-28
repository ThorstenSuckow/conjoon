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
 * @see Conjoon_Filter_ShortenString
 */
require_once 'Conjoon/Filter/ShortenString.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_ShortenStringTest extends PHPUnit_Framework_TestCase {

    /**
     * Conjoon_Filter_DateToUtc object
     *
     * @var Conjoon_Filter_DateToUtc
     */
    protected $_filter;

    /**
     * Creates a new Conjoon_Filter_ShortenString object for each test method
     *
     * @return void
     */
    public function setUp($length = null, $delimiter = null)
    {

        if ($length === null && $delimiter === null) {
            $this->_filter = new Conjoon_Filter_ShortenString();
            return;
        } else if ($delimiter === null) {
            $this->_filter = new Conjoon_Filter_ShortenString($length);
            return;
        }

        $this->_filter = new Conjoon_Filter_ShortenString($length, $delimiter);
    }

    /**
     * Ensure exception.
     *
     * @expectedException Conjoon_Filter_Exception
     */
    public function testFilterNoArgumentsException()
    {
        $this->_filter = new Conjoon_Filter_ShortenString("", null);
    }

    /**
     * Ensures everything works as expected
     *
     * @return void
     */
    public function testFilterNoConstructArguments()
    {
        $string = "Hello World";

        $this->assertEquals("Hello World", $this->_filter->filter($string));
    }

    /**
     * Ensures everything works as expected
     *
     * @return void
     */
    public function testFilterConstructArgumentLenght()
    {
        $string = "Hello World";

        $this->setUp(5);

        $this->assertEquals("He...", $this->_filter->filter($string));
    }

    /**
     * Ensures everything works as expected
     *
     * @return void
     */
    public function testFilterMinimumLength()
    {
        $string = "Hel";

        $this->setUp(3);

        $this->assertEquals("Hel", $this->_filter->filter($string));
    }

    /**
     * Ensures everything works as expected
     *
     * @return void
     */
    public function testFilterWithLastCharsEqualToDelimiter()
    {
        $string = "Hello.......";

        $this->setUp(9);

        $this->assertEquals("Hello...", $this->_filter->filter($string));
    }

    /**
     * Ensures everything works as expected
     *
     * @return void
     */
    public function testFilterWithTwoArguments()
    {
        $string = "Hello World";

        $this->setUp(5, '--');

        $this->assertEquals("Hel--", $this->_filter->filter($string));
    }
}
