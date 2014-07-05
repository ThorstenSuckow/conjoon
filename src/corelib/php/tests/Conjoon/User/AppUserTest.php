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

namespace Conjoon\User;

/**
 * @see Conjoon_User_AppUser
 */
require_once 'Conjoon/User/AppUser.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class AppUserTest extends \PHPUnit_Framework_TestCase {

    protected $_userData;

    protected $_defaultUser = null;

    protected $_failUser = null;

    protected function setUp()
    {
        $this->_userData = array(
            'id'          => 232323,
            'firstName'   => 'wwrwrw',
            'lastName'    => 'ssfsfsf',
            'username'    => 'fsfsf',
            'emailAddress' => 'fssffssf'
        );

        require_once 'Conjoon/Modules/Default/User.php';

        $this->_defaultUser = new \Conjoon_Modules_Default_User();

        $this->_defaultUser->setId($this->_userData['id']);
        $this->_defaultUser->setFirstName($this->_userData['firstName']);
        $this->_defaultUser->setLastName($this->_userData['lastName']);
        $this->_defaultUser->setEmailAddress($this->_userData['emailAddress']);
        $this->_defaultUser->setUserName($this->_userData['username']);

        $this->_failUser = new \Conjoon_Modules_Default_User();

    }

    /**
     * @expectedException \Conjoon_Argument_Exception
     */
    public function testConstructErrorArgument()
    {
        new AppUser("Bla");
    }

    /**
     * @expectedException \Conjoon\User\UserException
     */
    public function testConstructErrorUserData()
    {
        new AppUser($this->_failUser);
    }

    /**
     * Ensures everythingworks as expected
     */
    public function testConstructOk()
    {
        $appUser = new AppUser($this->_defaultUser);

        $this->assertTrue(is_int($this->_userData['id']));
        $this->assertSame((string)$this->_userData['id'], $appUser->getId());
        $this->assertEquals($this->_userData['firstName'], $appUser->getFirstName());
        $this->assertEquals($this->_userData['lastName'], $appUser->getLastName());
        $this->assertEquals($this->_userData['emailAddress'], $appUser->getEmailAddress());
        $this->assertEquals($this->_userData['username'], $appUser->getUsername());
    }

    /**
     * Ensures everythingworks as expected
     */
    public function test__toStringOk()
    {
        $appUser = new AppUser($this->_defaultUser);

        $val = $appUser->__toString();

        $this->assertFalse(empty($val));

        $this->assertTrue(is_string($val));
    }

}
