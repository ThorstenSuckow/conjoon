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


namespace Conjoon\Mail\Client\Account;

/**
 * @see \Conjoon\Mail\Client\Account\Account
 */
require_once 'Conjoon/Mail/Client/Account/Account.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class AccountTest extends \PHPUnit_Framework_TestCase {


    protected $className = '\Conjoon\Mail\Client\Account\Account';

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $id = 1234;

        $account = new $this->className(1234);

        $this->assertInstanceOf($this->className, $account);

        $this->assertSame($account->getId(), $id);

        $this->assertTrue(
            is_string($account->__toString())
        );
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstruct_args_null()
    {
        new $this->className(null);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstruct_args_lessThan1()
    {
        new $this->className(-1);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstruct_args_String()
    {
        new $this->className("1");
    }

}
