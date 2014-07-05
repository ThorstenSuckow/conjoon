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
 * @see Conjoon_Filter_DateToUtc
 */
require_once 'Conjoon/Filter/DateToUtc.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_DateToUtcTest extends PHPUnit_Framework_TestCase {

    /**
     * Conjoon_Filter_DateToUtc object
     *
     * @var Conjoon_Filter_DateToUtc
     */
    protected $_filter;

    /**
     * Creates a new Conjoon_Filter_DateToUtc object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Conjoon_Filter_DateToUtc();
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

    /**
     * Ensures eexception is thrown if invalid argument is passed.
     *
     * @expectedException Conjoon_Filter_Exception
     *
     * @return void
     */
    public function testException()
    {
        $this->_filter->filter("");
    }

}
