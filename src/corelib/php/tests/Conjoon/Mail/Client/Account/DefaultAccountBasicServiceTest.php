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

use Conjoon\Mail\Client\Account\TestAccountMockRepository;

/**
 * @see Conjoon\Mail\Client\Account\TestAccountMockRepository
 */
require_once 'Conjoon/Mail/Client/Account/TestAccountMockRepository.php';

/**
 * @see Conjoon\Mail\Client\Account\DefaultAccountBasicService
 */
require_once 'Conjoon/Mail/Client/Account/DefaultAccountBasicService.php';

/**
 * @see Conjoon\Mail\Client\Account\Account
 */
require_once 'Conjoon/Mail/Client/Account/Account.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAccountBasicServiceTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $basicService;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/DefaultAccountBasicServiceTest.xml'
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');

        $this->basicService = new DefaultAccountBasicService(array(
            'mailAccountRepository' => $repository
        ));
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructWithException_value()
    {
        new DefaultAccountBasicService(array('mailAccountRepository' => 'test'));
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructWithException_key()
    {
        new DefaultAccountBasicService(array('nope' => 'test'));
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Account\AccountDoesNotExistException
     */
    public function testGetAccountEntity_AccountDoesNotExistException() {

        $this->basicService->getAccountEntity(new Account(5000));

    }

    /**
     * @expectedException \Conjoon\Mail\Client\Account\AccountServiceException
     */
    public function testGetAccountEntity_AccountServiceException() {

        $repository = new TestAccountMockRepository();

        $basicService = new DefaultAccountBasicService(array(
            'mailAccountRepository' => $repository
        ));

        $basicService->getAccountEntity(new Account(5000));

    }

    /**
     * Ensures everything works as expected
     */
    public function testGetFolderEntity() {

        $this->assertSame(
            1,
            $this->basicService->getAccountEntity(new Account(1))->getId()
        );

    }

}