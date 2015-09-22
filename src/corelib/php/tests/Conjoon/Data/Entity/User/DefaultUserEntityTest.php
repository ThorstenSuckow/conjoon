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

namespace Conjoon\Data\Entity\User;

use Conjoon\User\AppUser;

/**
 * @see Conjoon\Data\Entity\User\DefaultUserEntity
 */
require_once 'Conjoon/Data/Entity/User/DefaultUserEntity.php';

/**
 * @see \Conjoon_Modules_Default_User
 */
require_once 'Conjoon/Modules/Default/User.php';

/**
 * @see \Conjoon\User\AppUser
 */
require_once 'Conjoon/User/AppUser.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultUserEntityTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected function setUp()
    {
        $this->input = array(
            'firstname'       => "name",
            'lastname'        => "Lastname",
            'emailAddress'    => "emailAddress",
            'userName'        => "UserName1",
            'password'        => "password",
            'isRoot'          => 1,
            'authToken'       => "authToken",
            'lastLogin'       => 2,
            'rememberMeToken' => 'rememberMeToken'
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $user = new DefaultUserEntity();

        foreach ($this->input as $field => $value) {
            $methodSet = "set" . ucfirst($field);
            $methodGet = "get" . ucfirst($field);
            $user->$methodSet($value);

            $this->assertSame($value, $user->$methodGet());
        }
    }

    /**
     * @ticket CN-963
     */
    public function testEquals_CN963() {

        $sameId = 1234;

        $user = new DefaultUserEntity();

        $reflector = new \ReflectionClass($user);
        $id = $reflector->getProperty('id');
        $id->setAccessible(true);
        $id->setValue($user, $sameId);

        $userData = array(
            'id'           => $sameId,
            'firstName'    => 'a',
            'lastName'     => 'b',
            'username'     => 'c',
            'emailAddress' => 'd'
        );

        $tmpUser = new \Conjoon_Modules_Default_User();
        $tmpUser->setId($userData['id']);
        $tmpUser->setFirstName($userData['firstName']);
        $tmpUser->setLastName($userData['lastName']);
        $tmpUser->setEmailAddress($userData['emailAddress']);
        $tmpUser->setUserName($userData['username']);

        $succUser = new AppUser($tmpUser);

        $tmpUser->setId($userData['id'] + 1);
        $failUser = new AppUser($tmpUser);


        $this->assertTrue($user->equals($succUser));
        $this->assertFalse($user->equals($failUser));
        $this->assertTrue($user->equals($user));

    }

}
