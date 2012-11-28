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
 * @see Conjoon\Data\Repository\Mail\DoctrineMessageFlagRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DoctrineMessageFlagRepository.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineMessageFlagRepositoryTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $repository;

    protected $userRepository;

    protected $messageRepository;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/messageflag.xml'
        );
    }

    public function setUp()
    {
        parent::setUp();

        $this->repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity');

        $this->userRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\User\DefaultUserEntity');

        $this->messageRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');


        $this->assertTrue($this->repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMessageFlagRepository);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testThrowException()
    {
        $entity = $this->repository->findById(array(1, 1));
    }

    /**
     * Ensure everything works as expected
     */
    public function testFindNone()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMessageFlagRepository);


        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Pre-Condition"
        );

        $entity = $repository->findById(array('messageId' => 231, 'userId' => 1));

        $this->assertSame(null, $entity);
    }

    /**
     * Ensure everything works as expected
     */
    public function testFindById()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMessageFlagRepository);


        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Pre-Condition"
        );

        $entity = $repository->findById(array('messageId' => 1, 'userId' => 1));


        $this->assertSame(1, $entity->getGroupwareEmailItems()->getId());
        $this->assertSame(1, $entity->getUsers()->getId());

    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistCreateByAddingToMessageAndPersistingWithMessage()
    {
        $message = $this->messageRepository->findById(1);
        $user    = $this->userRepository->findById(3);

        $flag = new \Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity();

        $message->addGroupwareEmailItemsFlag($flag);

        $flag->setUsers($user);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Pre-Condition"
        );

        $this->repository->persist($flag);

        $this->messageRepository->persist($message);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Pre-Condition"
        );

        $this->messageRepository->flush();

        $this->assertEquals(
            3,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Post-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_flags',
            'SELECT * FROM groupware_email_items_flags ORDER BY user_id'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/messageflag.add.result.xml'
        )->getTable("groupware_email_items_flags");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistCreateByAddingToMessage()
    {
        $message = $this->messageRepository->findById(1);
        $user    = $this->userRepository->findById(3);

        $flag = new \Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity();

        $message->addGroupwareEmailItemsFlag($flag);

        $flag->setUsers($user);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Pre-Condition"
        );

        $this->repository->persist($flag);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Pre-Condition"
        );

        $this->repository->flush();

        $this->assertEquals(
            3,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Post-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_flags',
            'SELECT * FROM groupware_email_items_flags ORDER BY user_id'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/messageflag.add.result.xml'
        )->getTable("groupware_email_items_flags");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistCreate()
    {
        $message = $this->messageRepository->findById(1);
        $user    = $this->userRepository->findById(3);

        $flag = new \Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity();

        $flag->setUsers($user);
        $flag->setGroupwareEmailItems($message);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Pre-Condition"
        );

        $this->repository->persist($flag);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Pre-Condition"
        );

        $this->repository->flush();

        $this->assertEquals(
            3,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Post-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_flags',
            'SELECT * FROM groupware_email_items_flags ORDER BY user_id'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/messageflag.add.result.xml'
        )->getTable("groupware_email_items_flags");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistUpdate()
    {
        $flag = $this->repository->findById(array(
            'messageId' => 1, 'userId' => 2
        ));

        $flag->setIsRead(0);
        $flag->setIsDeleted(0);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Pre-Condition"
        );

        $this->repository->persist($flag);

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_flags',
            'SELECT * FROM groupware_email_items_flags ORDER BY user_id'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/messageflag.xml'
        )->getTable("groupware_email_items_flags");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $this->repository->flush();

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Post-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_flags',
            'SELECT * FROM groupware_email_items_flags ORDER BY user_id'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/messageflag.update.result.xml'
        )->getTable("groupware_email_items_flags");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistUpdateByUsingMessageRepository()
    {
        $message = $this->messageRepository->findById(1);

        $flags = $message->getGroupwareEmailItemsFlags();

        $flag = null;
        for ($i = 0, $len = count($flags); $i < $len; $i++) {
            if ($flags[$i]->getUsers()->getId() == 2) {
                $flag = $flags[$i];
                break;
            }
        }

        $this->assertTrue($flag !== null);

        $this->assertSame(true, $flag->getIsRead());
        $this->assertSame(true, $flag->getIsDeleted());

        $flag->setIsRead(false);
        $flag->setIsDeleted(false);

        $this->messageRepository->persist($message);
        $this->messageRepository->flush();

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_flags',
            'SELECT * FROM groupware_email_items_flags ORDER BY user_id'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/messageflag.update.result.xml'
        )->getTable("groupware_email_items_flags");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testRemove()
    {
        $flag = $this->repository->findById(array(
            'messageId' => 1, 'userId' => 2
        ));

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Pre-Condition"
        );

        $this->repository->remove($flag);

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_flags',
            'SELECT * FROM groupware_email_items_flags ORDER BY user_id'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/messageflag.xml'
        )->getTable("groupware_email_items_flags");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $this->repository->flush();

        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount('groupware_email_items_flags'),
            "Post-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_flags',
            'SELECT * FROM groupware_email_items_flags ORDER BY user_id'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/messageflag.remove.result.xml'
        )->getTable("groupware_email_items_flags");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testRemoveByUsingMessageRepository()
    {
        $message = $this->messageRepository->findById(1);

        $flags = $message->getGroupwareEmailItemsFlags();

        $flag = null;
        for ($i = 0, $len = count($flags); $i < $len; $i++) {
            if ($flags[$i]->getUsers()->getId() == 2) {
                $flag = $flags[$i];
                break;
            }
        }

        $this->assertTrue($flag !== null);

        $this->assertSame(
            2,
            count($message->getGroupwareEmailItemsFlags())
        );

        $message->removeGroupwareEmailItemsFlag($flag);

        $this->messageRepository->persist($message);
        $this->messageRepository->flush();

        $this->assertSame(
            1,
            count($message->getGroupwareEmailItemsFlags())
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_flags',
            'SELECT * FROM groupware_email_items_flags ORDER BY user_id'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/messageflag.remove.result.xml'
        )->getTable("groupware_email_items_flags");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }


}