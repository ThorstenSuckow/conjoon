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

namespace Conjoon\Data\Repository\User;

/**
 * @see Conjoon\Data\Repository\User\DoctrineUserRepository
 */
require_once 'Conjoon/Data/Repository/User/DoctrineUserRepository.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineUserRepositoryTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $repository;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/user.xml'
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\User\DefaultUserEntity');

        $this->assertTrue($this->repository
            instanceof \Conjoon\Data\Repository\User\UserRepository);

        $this->assertEquals(
            2, $this->getConnection()->getRowCount('users'),
            "Pre-Condition"
        );

    }

    /**
     * Ensure everything works as expected
     */
    public function testFindNone()
    {
        $entity = $this->repository->findById(97809732);

        $this->assertSame(null, $entity);
    }

    /**
     * Ensure everything works as expected
     */
    public function testFindById()
    {
        $entity = $this->repository->findById(1);

        $this->assertSame(1, $entity->getId());
        $this->assertSame('Firstname', $entity->getFirstname());
        $this->assertSame('Password', $entity->getPassword());
        $this->assertSame('rememberMeToken1', $entity->getRememberMeToken());
    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistCreate()
    {
        $entity = new \Conjoon\Data\Entity\User\DefaultUserEntity();

        $entity->setFirstname("Firstname 3");
        $entity->setLastname("Lastname");
        $entity->setEmailAddress("EmailAddress");
        $entity->setUserName("UserName3");
        $entity->setPassword("Password");
        $entity->setAuthToken("AuthToken");
        $entity->setLastLogin(3);
        $entity->setRememberMeToken('rememberMeToken3');

        $this->repository->register($entity);

        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT * FROM users'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/user.xml'
        )->getTable("users");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $this->repository->flush();

        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT * FROM users'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/user.add.result.xml'
        )->getTable("users");
        $this->assertTablesEqual($expectedTable, $queryTable);

    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistUpdate()
    {
        $user = $this->repository->findById(2);

        $user->setFirstname("Firstname 2");

        $this->repository->register($user);

        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT * FROM users'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/user.xml'
        )->getTable("users");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $this->repository->flush($user);

        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT * FROM users'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/user.update.result.xml'
        )->getTable("users");
        $this->assertTablesEqual($expectedTable, $queryTable);

    }

    /**
     * Ensures everything works as expected.
     */
    public function testRemove()
    {
        $entity = $this->repository->findById(2);

        $this->repository->remove($entity);

        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT * FROM users'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/user.xml'
        )->getTable("users");
        $this->assertTablesEqual($expectedTable, $queryTable);

        // FLUSH
        $this->repository->flush($entity);

        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT * FROM users'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/user.remove.result.xml'
        )->getTable("users");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

}
