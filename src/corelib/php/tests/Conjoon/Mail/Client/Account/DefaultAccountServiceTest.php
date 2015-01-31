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
 * @see Conjoon\Mail\Client\Account\DefaultAccountService
 */
require_once 'Conjoon/Mail/Client/Account/DefaultAccountService.php';

/**
 * @package Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAccountServiceTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $service;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/account.xml'
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->service = new DefaultAccountService(
            array(
                'user'          => new UserMock(1),
                'mailAccountRepository' => $this->_entityManager->getRepository(
                    '\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity'),
                'folderService' => new \Conjoon\Mail\Client\Folder\DefaultFolderService(array(
                    'user'                 => new UserMock(),
                    'mailFolderCommons'    => new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(
                        array(
                        'user' => new UserMock(),
                        'mailFolderRepository' => $this->_entityManager->getRepository(
                            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity'
                        ))
                    ),
                    'mailFolderRepository' => $this->_entityManager->getRepository(
                        '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity'
        )))));

    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testContructor_Exception()
    {
        new DefaultAccountService(array(
            'user' => new UserMock()
        ));
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Account\AccountServiceException
     */
    public function testGetMailAccountToAccessRemoteFolder_Exception()
    {
        $this->service->getMailAccountToAccessRemoteFolder(
            new \Conjoon\Mail\Client\Folder\Folder(
                new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                    '["root", "3", "4"]'
        )));
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Account\AccountServiceException
     */
    public function testGetMailAccountToAccessRemoteFolder_Exception2()
    {
        $this->service->getMailAccountToAccessRemoteFolder(
            new \Conjoon\Mail\Client\Folder\Folder(
                new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                    '["root", "fgdgf", "fdfdf"]'
                )));
    }

    /**
     * Ensure everything works as expected.
     */
    public function testGetMailAccountToAccessRemoteFolder_Ok()
    {
        $this->assertNull($this->service->getMailAccountToAccessRemoteFolder(
            new \Conjoon\Mail\Client\Folder\Folder(
                new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                    '["root", "5", "6"]'
                ))));

        $this->assertSame(
            1,
            $this->service->getMailAccountToAccessRemoteFolder(
            new \Conjoon\Mail\Client\Folder\Folder(
                new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                    '["root", "1", "2"]'
                )))->getId());

        $this->assertSame(
            1,
            $this->service->getMailAccountToAccessRemoteFolder(
                new \Conjoon\Mail\Client\Folder\Folder(
                    new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                        '["root", "1"]'
                    )))->getId());
    }

    public function testGetStandardMailAccount()
    {
        $this->assertSame(1, $this->service->getStandardMailAccount()->getId());
    }

    public function testGetMailAccounts()
    {
        $this->assertTrue(is_array($this->service->getMailAccounts()));
        $this->assertSame(2, count($this->service->getMailAccounts()));
    }

    public function testGetMailAccountForMailAddress_Exception() {

        $values = array("", null, 123);

        foreach ($values as $value) {
            $e = null;
            try {
                $this->service->getMailAccountForMailAddress($value);
            } catch (\Exception $e) {}

            $this->assertTrue(
                $e instanceof
                \Conjoon\Mail\Client\Account\AccountServiceException);
        }

    }

    public function testGetMailAccountForMailAddress()
    {
        $this->assertSame(1,
            $this->service->getMailAccountForMailAddress('reply_address')->getId());
        $this->assertSame(2,
            $this->service->getMailAccountForMailAddress('reply_address2')->getId());
        $this->assertSame(2,
            $this->service->getMailAccountForMailAddress('address2')->getId());
        $this->assertNull(
            $this->service->getMailAccountForMailAddress('something'));
    }


}

class UserMock implements \Conjoon\User\User {

    protected $userId;

    public function __construct($userId = null)
    {
        $this->userId = $userId;
    }

    public function getId(){if ($this->userId !== null)return $this->userId;}

    public function getFirstname(){}

    public function getLastname(){}

    public function getEmailAddress(){}

    public function getUserName(){}

    public function __toString()
    {
        return "" . $this->userId;
    }

}