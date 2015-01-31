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
 * @see Conjoon\Data\Repository\Mail\DoctrineMessageRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DoctrineMessageRepository.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineMessageRepositoryTest extends \Conjoon\DatabaseTestCaseDefault {

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.xml'
        );
    }

    /**
     * Ensure everything works as expected
     */
    public function testFindNone()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMessageRepository);


        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount('groupware_email_items'),
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
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMessageRepository);


        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount('groupware_email_items'),
            "Pre-Condition"
        );

        $entity = $repository->findById(1);

        $this->assertSame(1, $entity->getId());
        $this->assertSame("Subject", $entity->getSubject());
        $this->assertSame("From", $entity->getFrom());
        $this->assertSame("To", $entity->getTo());
        $this->assertSame("Cc", $entity->getCc());
        $this->assertSame("Bcc", $entity->getBcc());
        $this->assertSame("In_reply_to", $entity->getInReplyTo());
        $this->assertSame("References", $entity->getReferences());
        $this->assertSame("Content_text_plain", $entity->getContentTextPlain());
        $this->assertSame("Content_text_html", $entity->getContentTextHtml());
        $this->assertSame("Recipients", $entity->getRecipients());
        $this->assertSame("Sender", $entity->getSender());


        $folder = $entity->getGroupwareEmailFolders();
        $this->assertSame(1, $folder->getId());

        $this->assertTrue($entity->getDate() instanceof \DateTime);
        $this->assertSame('1167606001', $entity->getDate()->format('U'));

        $flags = $entity->getGroupwareEmailItemsFlags();

        $this->assertSame(2, count($flags));

        $this->assertSame($entity->getId(), $flags[0]->getGroupwareEmailItems()->getId());
        $this->assertSame($entity->getId(), $flags[1]->getGroupwareEmailItems()->getId());

        $this->assertSame(1, $flags[0]->getUsers()->getId());
        $this->assertSame(2, $flags[1]->getUsers()->getId());

    }

    /**
     * @expectedException Doctrine\ORM\ORMInvalidArgumentException
     */
    public function testRegisterCreateMailFolderException()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');

        $message = new \Conjoon\Data\Entity\Mail\DefaultMessageEntity();
        $folder = new \Conjoon\Data\Entity\Mail\DefaultMailFolderEntity();

        $message->setGroupwareEmailFolders($folder);

        $repository->register($message);
        $repository->flush();
    }

    /**
     * Ensures everything works as expected.
     */
    public function testRegisterCreate()
    {
        $this->getConnection();

        $folderRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMessageRepository);


        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount('groupware_email_items'),
            "Pre-Condition"
        );

        $message = new \Conjoon\Data\Entity\Mail\DefaultMessageEntity();

        $folder = $folderRepository->findById(1);

        $message->setGroupwareEmailFolders($folder);
        $message->setDate(new \DateTime("2007-01-01 00:00:02"));
        $message->setSubject("Subject2");
        $message->setFrom("From2");
        $message->setReplyTo("Reply_to2");
        $message->setTo("To2");
        $message->setCc("Cc2");
        $message->setBcc("Bcc2");
        $message->setInReplyTo("In_reply_to2");
        $message->setReferences("References2");
        $message->setContentTextPlain("Content_text_plain2");
        $message->setContentTextHtml("Content_text_html2");
        $message->setRecipients("Recipients2");
        $message->setSender("Sender2");

        $repository->register($message);

        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount('groupware_email_items'),
            "Pre-Condition"
        );

        $repository->flush();

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items', 'SELECT * FROM groupware_email_items'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.add.result.xml'
        )->getTable("groupware_email_items");
        $this->assertTablesEqual($expectedTable, $queryTable);

    }

    /**
     * Ensures everything works as expected.
     */
    public function testRegisteUpdate()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMessageRepository);


        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount('groupware_email_items'),
            "Pre-Condition"
        );

        $message = $repository->findById(1);

        $message->setSubject("New Subject");

        $repository->register($message);

        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount('groupware_email_items'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items', 'SELECT * FROM groupware_email_items'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.xml'
        )->getTable("groupware_email_items");
        $this->assertTablesEqual($expectedTable, $queryTable);


        $repository->flush($message);

        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount('groupware_email_items'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items', 'SELECT * FROM groupware_email_items'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.update.result.xml'
        )->getTable("groupware_email_items");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testRemove()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMessageRepository);


        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount('groupware_email_items'),
            "Pre-Condition"
        );

        $message = $repository->findById(1);
        $repository->remove($message);

        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount('groupware_email_items'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items', 'SELECT * FROM groupware_email_items'
        );
        $queryTableAttachments = $this->getConnection()->createQueryTable(
            'groupware_email_items_attachments',
            'SELECT * FROM groupware_email_items_attachments'
        );

        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.xml'
        )->getTable("groupware_email_items");
        $expectedTableAttachments = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.xml'
        )->getTable("groupware_email_items_attachments");

        $this->assertTablesEqual($expectedTable, $queryTable);
        $this->assertTablesEqual($expectedTableAttachments, $queryTableAttachments);

        $repository->flush();

        $this->assertEquals(
            0,
            $this->getConnection()->getRowCount('groupware_email_items'),
            "Pre-Condition"
        );
        $this->assertEquals(
            0,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items', 'SELECT * FROM groupware_email_items'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.remove.result.xml'
        )->getTable("groupware_email_items");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_flags', 'SELECT * FROM groupware_email_items_flags'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.remove.result.xml'
        )->getTable("groupware_email_items_flags");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_attachments', 'SELECT * FROM groupware_email_items_attachments'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.remove.result.xml'
        )->getTable("groupware_email_items_attachments");
        $this->assertTablesEqual($expectedTable, $queryTable);

        // groupware email folders
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders', 'SELECT * FROM groupware_email_folders'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.remove.result.xml'
        )->getTable("groupware_email_folders");
        $this->assertTablesEqual($expectedTable, $queryTable);

    }

}
