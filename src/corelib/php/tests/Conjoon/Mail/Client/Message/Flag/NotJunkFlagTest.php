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


namespace Conjoon\Mail\Client\Message\Flag;

/**
 * @see Conjoon\Mail\Client\Message\Flag\NotJunkFlag
 */
require_once 'Conjoon/Mail/Client/Message/Flag/NotJunkFlag.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class NotJunkFlagTest extends \PHPUnit_Framework_TestCase {

    protected $_input;

    protected function setUp()
    {
        $this->_input = array(
            'uId' => "hklhkl",
            'clear'     => true
        );
    }

    /**
     * @expectedException \Conjoon_Argument_Exception
     */
    public function testConstructFailFirst()
    {
        new JunkFlag("", true);
    }

    /**
     * Ensures everything works as expected
     */
    public function testConstructOk()
    {
        $flag = new NotJunkFlag(
            $this->_input['uId'],
            $this->_input['clear']
        );

        $this->assertTrue($flag instanceof \Conjoon\Mail\Message\Flag\NotJunkFlag);

        $this->assertSame('$NotJunk', $flag->__toString());
        $this->assertSame($this->_input['uId'], $flag->getUId());
        $this->assertSame($this->_input['clear'], $flag->isClear());

    }

}
