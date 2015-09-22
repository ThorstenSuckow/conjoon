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


namespace Conjoon\Mail\Client\Security;

use Conjoon\Mail\Client\Account\DefaultAccountBasicService,
    Conjoon\Mail\Client\Account\Account,
    Conjoon\Mail\Client\Account\TestAccountMockRepository;

/**
 * @see Conjoon\Mail\Client\Account\TestAccountMockRepository
 */
require_once 'Conjoon/Mail/Client/Account/TestAccountMockRepository.php';

/**
 * @see DefaultAccountSecurityService
 */
require_once 'Conjoon/Mail/Client/Security/DefaultAccountSecurityService.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @see Conjoon_Modules_Default_User
 */
require_once 'Conjoon/Modules/Default/User.php';

/**
 * @see \Conjoon\User\AppUser
 */
require_once 'Conjoon/User/AppUser.php';

/**
 * @see \Conjoon\Mail\Client\Account\Account
 */
require_once 'Conjoon/Mail/Client/Account/Account.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Security
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAccountSecurityServiceTest
    extends \Conjoon\DatabaseTestCaseDefault {


    protected $securityService;

    protected $user;

    protected $failUser;

    protected $basicService;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/DefaultAccountSecurityServiceTest.xml'
        );
    }


    protected function setUp()
    {
        parent::setUp();

        $user = new \Conjoon_Modules_Default_User();
        $user->setId(1);
        $user->setFirstName("a");
        $user->setLastName("b");
        $user->setUsername("c");
        $user->setEmailAddress("d");

        $user = new \Conjoon\User\AppUser($user);
        $this->user = $user;

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');

        $basicService = new DefaultAccountBasicService(array(
            'mailAccountRepository' => $repository
        ));

        $this->securityService = new DefaultAccountSecurityService(array(
            'user'                 => $user,
            'accountBasicService'  => $basicService
        ));


    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstruct_noValidBasicService() {
        new DefaultAccountSecurityService(array(
            'user'                 => $this->user,
            'accountServiceKeyWrong'  => $this->basicService
        ));
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstruct_noValidUser() {
        new DefaultAccountSecurityService(array(
            'user'                 => null,
            'accountBasicService'  => $this->basicService
        ));
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Security\AccountSecurityServiceException
     */
    public function testIsAccountAccessible_accountSecurityServiceException() {

        $repository = new \Conjoon\Mail\Client\Account\TestAccountMockRepository;

        $basicService = new DefaultAccountBasicService(array(
            'mailAccountRepository' => $repository
        ));

        $securityService = new DefaultAccountSecurityService(array(
            'user'                 => $this->user,
            'accountBasicService'  => $basicService
        ));

        $securityService->isAccountAccessible(new Account(1));
    }


    /**
     * @expectedException \Conjoon\Mail\Client\Account\AccountDoesNotExistException
     */
    public function testIsAccountAccessible_notExistingAccount() {

        $this->securityService->isAccountAccessible(new Account(112211));
    }

    /**
     * Ensure everything works as expected
     */
    public function testIsAccountAccessible() {

        $this->assertTrue($this->securityService->isAccountAccessible(
            new Account(1)
        ));

        $this->assertFalse($this->securityService->isAccountAccessible(
            new Account(2)
        ));

        $this->assertFalse($this->securityService->isAccountAccessible(
            new Account(3)
        ));
    }


    /**
     * Ensure everything works as expected
     */
    public function testGetUser() {

        $this->assertSame($this->user, $this->securityService->getUser());

    }


}
