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
 * @see Conjoon_Filter_DateUtc
 */
require_once 'Conjoon/Filter/DateUtc.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_DateUtcTest extends PHPUnit_Framework_TestCase {

    /**
     * Conjoon_Filter_DateUtc object
     *
     * @var Conjoon_Filter_DateUtc
     */
    protected $_filter;

    /**
     * Creates a new Conjoon_Filter_DateUtc object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Conjoon_Filter_DateUtc();
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

    /**
     * Ensures everything works as expected
     *
     * @return void
     */
    public function testFilter()
    {
        $asserts = array(
            "Fri, 13 Dec 1901 20:45:54 GMT"   => "1901-12-13 20:45:54",
            "Tue, 19 Jan 2038 03:14:07 GMT"   => "2038-01-19 03:14:07",
            "2 Nov 2011 07:17:22 -0000"       => "2011-11-02 07:17:22",
            "Fri Nov 04 09:24:21 2011"        => "2011-11-04 09:24:21",
            "Tue, 01 Nov 2011 16:20:06 +0100" => "2011-11-01 15:20:06",
            "Tue, 01 Nov 2011 16:00:16 +0100" => "2011-11-01 15:00:16",
            "Mon, 20 Jun 2011 16:09:38 +0200" => "2011-06-20 14:09:38",
            "01.02.2010"                      => "2010-02-01 00:00:00",
            "01-02-2010"                      => "2010-02-01 00:00:00",
            "01-02-2010 PST"                  => "2010-02-01 08:00:00"
        );

        foreach ($asserts as $input => $output) {
            $this->assertEquals($output, $this->_filter->filter($input));
        }
    }

}
