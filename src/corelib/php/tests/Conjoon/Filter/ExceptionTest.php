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
 * @see Conjoon_Filter_Exception
 */
require_once 'Conjoon/Filter/Exception.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_ExceptionTest extends PHPUnit_Framework_TestCase {

    /**
     * Conjoon_Filter_Exception object
     *
     * @var Conjoon_Filter_Exception
     */
    protected $_exception;

    /**
     * Creates a new Conjoon_Filter_Exception object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_exception = new Conjoon_Filter_Exception();
    }

    /**
     * Ensures that the exception derives from Conjoon_Exception
     *
     * @return void
     */
    public function testParentClass()
    {
        $this->assertTrue(
            $this->_exception instanceof Conjoon_Exception
        );
    }
}
