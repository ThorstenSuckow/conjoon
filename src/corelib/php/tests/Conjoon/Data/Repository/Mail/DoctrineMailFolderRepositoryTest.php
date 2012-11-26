<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
 * licensing@conjoon.org
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

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        );
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
            3,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );

        $entity = $repository->findById(3);

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
            3,
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
        $repository->persist($entity);
        $this->assertEquals(
            3,
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
        $this->assertEquals(4,
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

        $repository->persist($entity);

        $this->assertEquals(
            3,
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
            3,
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
        $this->assertEquals(
            0,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Post-Condition"
        );


    }

}