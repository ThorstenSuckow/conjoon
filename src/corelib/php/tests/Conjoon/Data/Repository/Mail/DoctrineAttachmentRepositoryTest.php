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
 * @see Conjoon\Data\Repository\Mail\DoctrineAttachmentRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DoctrineAttachmentRepository.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineAttachmentRepositoryTest extends \Conjoon\DatabaseTestCaseDefault {

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.xml'
        );
    }

    /**
     * helper
     */
    protected function createHelperMessage() {

        $folderRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $message = new \Conjoon\Data\Entity\Mail\DefaultMessageEntity();

        $folder = $folderRepository->findById(1);

        $message->setGroupwareEmailFolders($folder);
        $message->setDate(new \DateTime("2007-01-01 00:00:02"));
        $message->setSubject("glglglSubject");
        $message->setFrom("From");
        $message->setReplyTo("Reply_to");
        $message->setTo("To");
        $message->setCc("Cc");
        $message->setBcc("Bcc");
        $message->setInReplyTo("In_reply_to");
        $message->setReferences("References");
        $message->setContentTextPlain("Content_text_plain");
        $message->setContentTextHtml("Content_text_html");
        $message->setRecipients("Recipients");
        $message->setSender("Sender");

        return $message;
    }

    /**
     * Ensure everything works as expected
     */
    public function testFindNone()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineAttachmentRepository);


        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
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
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineAttachmentRepository);


        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $entity = $repository->findById(1);

        $this->assertSame(1, $entity->getId());
        $this->assertSame("key1", $entity->getKey());
        $this->assertSame("filename", $entity->getFileName());
        $this->assertSame("mimetype", $entity->getMimeType());
        $this->assertSame("encoding", $entity->getEncoding());
        $this->assertSame("contentid", $entity->getContentId());

        $content = $entity->getAttachmentContent();
        $this->assertSame(1, $content->getId());
    }

    /**
     * Ensure everything works as expected.
     */
    public function testFindByIdAsAttachmentLocationNoResult() {

        $attachmentLocation1 =
            new \Conjoon\Mail\Client\Message\DefaultAttachmentLocation(
                new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                    new \Conjoon\Mail\Client\Folder\Folder(
                        new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                            '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
                        )
                    ), "1"
                ), "key11223"
            );

        $attachmentLocation2 =
            new \Conjoon\Mail\Client\Message\DefaultAttachmentLocation(
                new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                    new \Conjoon\Mail\Client\Folder\Folder(
                        new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                            '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
                        )
                    ), "123"
                ), "key1"
            );


        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');

        $entity1 = $repository->findById($attachmentLocation1);

        $this->assertNull($entity1);

        $entity2 = $repository->findById($attachmentLocation2);

        $this->assertNull($entity2);
    }

    /**
     * Ensure everything works as expected
     */
    public function testFindByIdAsAttachmentLocation()
    {
        $attachmentLocation =
            new \Conjoon\Mail\Client\Message\DefaultAttachmentLocation(
                new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                    new \Conjoon\Mail\Client\Folder\Folder(
                        new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                            '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
                        )
                    ), "1"
                ), "key1"
            );


        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');

        $entity = $repository->findById($attachmentLocation);

        $this->assertSame(1, $entity->getId());
        $this->assertSame("key1", $entity->getKey());
        $this->assertSame("filename", $entity->getFileName());
        $this->assertSame("mimetype", $entity->getMimeType());
        $this->assertSame("encoding", $entity->getEncoding());
        $this->assertSame("contentid", $entity->getContentId());

        $content = $entity->getAttachmentContent();
        $this->assertSame(1, $content->getId());
    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistCreateUsingAttachmentRepository()
    {
        $messageRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');


        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');


        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineAttachmentRepository);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $message = $this->createHelperMessage();
        $messageRepository->register($message);
        $messageRepository->flush();

        $attachment = new \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity();
        $attachmentContent = new \Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity();
        $attachmentContent->setContent('BLOB3');

        $attachment->setKey('key3');
        $attachment->setMimeType('mimetype');
        $attachment->setFileName('filename');
        $attachment->setEncoding('encoding');
        $attachment->setContentId('contentid');
        $attachment->setAttachmentContent($attachmentContent);

        $attachment->setMessage($message);//->addAttachment($attachment);

        $repository->register($attachment);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $repository->flush();


        $this->assertEquals(
            3,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_attachments', 'SELECT * FROM groupware_email_items_attachments'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/attachment.add.result.xml'
        )->getTable("groupware_email_items_attachments");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $queryTable = $this->getConnection()->createQueryTable(
            'mail_attachment_content', 'SELECT * FROM mail_attachment_content'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/attachment.add.result.xml'
        )->getTable("mail_attachment_content");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistCreateUsingMessage()
    {
        $messageRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');

        $message = $this->createHelperMessage();

        $attachment = new \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity();
        $attachmentContent = new \Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity();
        $attachmentContent->setContent('BLOB3');

        $attachment->setKey('key3');
        $attachment->setMimeType('mimetype');
        $attachment->setFileName('filename');
        $attachment->setEncoding('encoding');
        $attachment->setContentId('contentid');
        $attachment->setAttachmentContent($attachmentContent);
        $message->addAttachment($attachment);


        $messageRepository->register($message);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $messageRepository->flush();


        $this->assertEquals(
            3,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_attachments', 'SELECT * FROM groupware_email_items_attachments'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/attachment.add.result.xml'
        )->getTable("groupware_email_items_attachments");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $queryTable = $this->getConnection()->createQueryTable(
            'mail_attachment_content', 'SELECT * FROM mail_attachment_content'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/attachment.add.result.xml'
        )->getTable("mail_attachment_content");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistCreateUsingMessageByFind()
    {
        $messageRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');


        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');

        $message = $messageRepository->findById(1);

        $attachment = new \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity();
        $attachmentContent = new \Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity();
        $attachmentContent->setContent('BLOB3');

        $attachment->setKey('key3');
        $attachment->setMimeType('mimetype');
        $attachment->setFileName('filename');
        $attachment->setEncoding('encoding');
        $attachment->setContentId('contentid');
        $attachment->setAttachmentContent($attachmentContent);
        $message->addAttachment($attachment);

        $messageRepository->register($message);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $messageRepository->flush();


        $this->assertEquals(
            3,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_attachments', 'SELECT * FROM groupware_email_items_attachments'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/attachment.addbyfind.result.xml'
        )->getTable("groupware_email_items_attachments");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $queryTable = $this->getConnection()->createQueryTable(
            'mail_attachment_content', 'SELECT * FROM mail_attachment_content'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/attachment.add.result.xml'
        )->getTable("mail_attachment_content");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistUpdate()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineAttachmentRepository);


        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $attachment = $repository->findById(1);

        $attachment->setKey("New Key");

        $repository->register($attachment);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_attachments', 'SELECT * FROM groupware_email_items_attachments'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.xml'
        )->getTable("groupware_email_items_attachments");
        $this->assertTablesEqual($expectedTable, $queryTable);


        $repository->flush($attachment);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_attachments', 'SELECT * FROM groupware_email_items_attachments'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/attachment.update.result.xml'
        )->getTable("groupware_email_items_attachments");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testRemove()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineAttachmentRepository);


        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $attachment = $repository->findById(1);

        $repository->remove($attachment);

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $queryTableAttachments = $this->getConnection()->createQueryTable(
            'groupware_email_items_attachments',
            'SELECT * FROM groupware_email_items_attachments'
        );

        $expectedTableAttachments = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.xml'
        )->getTable("groupware_email_items_attachments");

        $this->assertTablesEqual($expectedTableAttachments, $queryTableAttachments);

        $repository->flush($attachment);

        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount('groupware_email_items_attachments'),
            "Pre-Condition"
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_attachments', 'SELECT * FROM groupware_email_items_attachments'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/attachment.remove.result.xml'
        )->getTable("groupware_email_items_attachments");
        $this->assertTablesEqual($expectedTable, $queryTable);

    }

}
