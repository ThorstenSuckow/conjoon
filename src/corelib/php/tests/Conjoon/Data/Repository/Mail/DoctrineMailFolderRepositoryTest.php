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

namespace Conjoon\Data\Repository\Mail;

/**
 * @see Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DoctrineMailFolderRepository.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineMailFolderRepositoryTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $mailFolderCount = 5;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        );
    }

    /**
     * Ensure everything works as expected.
     */
    public function testHasMessages() {

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $folder1 = $repository->findById(1);
        $folder2 = $repository->findById(5);

        $this->assertNotNull($folder1);
        $this->assertNotNull($folder2);

        $this->assertTrue($repository->hasMessages($folder1));
        $this->assertTrue($repository->hasMessages($folder2));

    }

    /**
     * Ensure everything works as expected.
     */
    public function testHasNoMessages() {

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $folder1 = $repository->findById(4);
        $folder2 = $repository->findById(3);
        $folder3 = $repository->findById(2);

        $this->assertNotNull($folder1);
        $this->assertNotNull($folder2);
        $this->assertNotNull($folder3);

        $this->assertFalse($repository->hasMessages($folder1));
        $this->assertFalse($repository->hasMessages($folder2));
        $this->assertFalse($repository->hasMessages($folder3));

    }

    /**
     * Ensure everything works as expected
     */
    public function testFindNone()
    {

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository);


        $this->assertEquals(
            $this->mailFolderCount,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );

        $entity = $repository->findById(97809732);

        $this->assertSame(null, $entity);
    }

    /**
     * Ensure everything works as expected
     */
    public function testFindById()
    {

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository);


        $this->assertEquals(
            $this->mailFolderCount,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );

        $entity = $repository->findById(3);

        $this->assertSame(
            2, count($entity->getMailAccounts())
        );

        $accounts = $entity->getMailAccounts();
        $this->assertSame(1, $accounts[0]->getId());

        $this->assertSame(3, $entity->getId());
        $this->assertSame('folder 3', $entity->getName());
        $this->assertSame(true, $entity->getIsChildAllowed());
        $this->assertSame(false, $entity->getIsLocked());
        $this->assertSame('root', $entity->getType());
        $this->assertSame('inbox', $entity->getMetaInfo());
        $this->assertSame(false, $entity->getIsDeleted());

        $entity = $entity->getParent();

        $this->assertTrue(
            $entity instanceof \Conjoon\Data\Entity\EntityProxy
        );

        $this->assertSame(2, $entity->getId());
        $this->assertSame('folder 2', $entity->getName());
        $this->assertSame(true, $entity->getIsChildAllowed());
        $this->assertSame(false, $entity->getIsLocked());
        $this->assertSame('root', $entity->getType());
        $this->assertSame('inbox', $entity->getMetaInfo());
        $this->assertSame(false, $entity->getIsDeleted());

        $entity = $entity->getParent();

        $this->assertTrue(
            $entity instanceof \Conjoon\Data\Entity\EntityProxy
        );

        $this->assertSame(1, $entity->getId());
        $this->assertSame('folder 1', $entity->getName());
        $this->assertSame(true, $entity->getIsChildAllowed());
        $this->assertSame(false, $entity->getIsLocked());
        $this->assertSame('root', $entity->getType());
        $this->assertSame('inbox', $entity->getMetaInfo());
        $this->assertSame(false, $entity->getIsDeleted());
        $this->assertSame(null, $entity->getParent());

    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistCreate()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository);


        $this->assertEquals(
            $this->mailFolderCount,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );

        $entity = new \Conjoon\Data\Entity\Mail\DefaultMailFolderEntity();

        $entity->setName("newname");
        $entity->setType("inbox");
        $entity->setMetaInfo("inbox");
        $entity->setIsChildAllowed(1);
        $entity->setIsLocked(0);
        $entity->setIsDeleted(0);

        // PERSIST
        $repository->register($entity);
        $this->assertEquals(
            $this->mailFolderCount,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders', 'SELECT * FROM groupware_email_folders'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        )->getTable("groupware_email_folders");
        $this->assertTablesEqual($expectedTable, $queryTable);

        // FLUSH
        $repository->flush();
        $this->assertEquals($this->mailFolderCount + 1,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Post-Condition"
        );
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders', 'SELECT * FROM groupware_email_folders'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.add.result.xml'
        )->getTable("groupware_email_folders");
        $this->assertTablesEqual($expectedTable, $queryTable);

    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistUpdate()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository);


        $entity = $repository->findById(1);

        $entity->setName("testchange");

        $repository->register($entity);

        $this->assertEquals(
            $this->mailFolderCount,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders', 'SELECT * FROM groupware_email_folders'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        )->getTable("groupware_email_folders");
        $this->assertTablesEqual($expectedTable, $queryTable);

        // FLUSH
        $repository->flush();
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders', 'SELECT * FROM groupware_email_folders'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.update.result.xml'
        )->getTable("groupware_email_folders");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testRemove()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository);


        $entity = $repository->findById(3);

        $repository->remove($entity);

        $this->assertEquals(
            $this->mailFolderCount,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );
        // FLUSH
        $repository->flush();
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders', 'SELECT * FROM groupware_email_folders'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.remove.result.xml'
        )->getTable("groupware_email_folders");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $entity = $repository->findById(1);

        $repository->remove($entity);
        $repository->flush();

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items', 'SELECT * FROM groupware_email_items'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.remove.result.xml'
        )->getTable("groupware_email_items");
        $this->assertTablesEqual($expectedTable, $queryTable);


        $this->assertEquals(
            0,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Post-Condition"
        );
    }

    /**
     * Ensure everything works as expected
     */
    public function testRemoveAndAddMailAccount() {

        $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        );
        $accountRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');


        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository);

        $this->assertTrue($accountRepository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMailAccountRepository);

        $entity = $repository->findById(1);
        $entity2 = $repository->findById(3);

        $accountEntity = $accountRepository->findById(2);

        $entity->removeMailAccount($accountEntity);
        $entity2->removeMailAccount($accountEntity);

        $repository->register($entity);
        $repository->register($entity2);


        $this->assertEquals(
            4,
            $this->getConnection()->getRowCount('groupware_email_folders_accounts'),
            "Pre-Condition"
        );
        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_accounts'),
            "Pre-Condition 2"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders_accounts', 'SELECT * FROM groupware_email_folders_accounts'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        )->getTable("groupware_email_folders_accounts");
        $this->assertTablesEqual($expectedTable, $queryTable);

        // FLUSH
        $repository->flush();
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders_accounts', 'SELECT * FROM groupware_email_folders_accounts'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.removeAccountResult.xml'
        )->getTable("groupware_email_folders_accounts");
        $this->assertTablesEqual($expectedTable, $queryTable);
        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_accounts')
        );


        // add the accounts again
        $entity->addMailAccount($accountEntity);
        $entity2->addMailAccount($accountEntity);

        $repository->register($entity);
        $repository->register($entity2);


        $repository->flush();
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders_accounts', 'SELECT * FROM groupware_email_folders_accounts'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        )->getTable("groupware_email_folders_accounts");
        $this->assertTablesEqual($expectedTable, $queryTable);

    }

    /**
     * Ensure everything works as expected.
     */
    public function testGetChildFolders() {

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $entity = $repository->findById(1);

        $entities = $repository->getChildFolders($entity);

        $this->assertSame(2, count($entities));

        $ids = array($entities[0]->getId(), $entities[1]->getId());

        $this->assertTrue(in_array(2, $ids));
        $this->assertTrue(in_array(4, $ids));
    }

    /**
     * Ensure everything works as expected.
     */
    public function testGetChildFoldersNone() {

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $entity = $repository->findById(5);

        $entities = $repository->getChildFolders($entity);

        $this->assertTrue(empty($entities));

        $this->assertSame(0, count($entities));

    }
}