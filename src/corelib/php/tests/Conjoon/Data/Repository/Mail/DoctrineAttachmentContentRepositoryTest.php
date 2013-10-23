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
 * @see Conjoon\Data\Repository\Mail\DoctrineAttachmentContentRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DoctrineAttachmentContentRepository.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineAttachmentContentRepositoryTest extends \Conjoon\DatabaseTestCaseDefault {

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

    }

    /**
     * Ensures everything works as expected
     */
    public function testProperRepository() {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity');

        $this->assertTrue($repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineAttachmentContentRepository);
    }

    /**
     * Ensure everything works as expected
     */
    public function testFindNone()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity');

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('mail_attachment_content'),
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
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity');

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('mail_attachment_content'),
            "Pre-Condition"
        );

        $entity = $repository->findById(1);

        $this->assertSame(1, $entity->getId());

        $stream = $entity->getContent();
        $c = stream_get_contents($stream);

        $this->assertSame("BLOB1", $c);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testUpdate()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity');

        $attachmentContent = $repository->findById(1);

        $attachmentContent->setContent('BLOB3');

        $repository->register($attachmentContent);

        $repository->flush();

        $queryTable = $this->getConnection()->createQueryTable(
            'mail_attachment_content', 'SELECT * FROM mail_attachment_content'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/attachmentcontent.update.result.xml'
        )->getTable("mail_attachment_content");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }


    /**
     * Ensures everything works as expected.
     */
    public function testRemove()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity');

        $attachmentContent = $repository->findById(1);

        $repository->remove($attachmentContent);

        $repository->flush();

        $queryTable = $this->getConnection()->createQueryTable(
            'mail_attachment_content', 'SELECT * FROM mail_attachment_content'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/attachmentcontent.remove.result.xml'
        )->getTable("mail_attachment_content");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testAdd()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity');

        $attachmentContent = new \Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity;
        $attachmentContent->setContent('BLOB3');

        $repository->register($attachmentContent);

        $repository->flush();

        $queryTable = $this->getConnection()->createQueryTable(
            'mail_attachment_content', 'SELECT * FROM mail_attachment_content'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/attachmentcontent.add.result.xml'
        )->getTable("mail_attachment_content");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testAddNoRegister()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity');

        $attachmentContent = new \Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity;
        $attachmentContent->setContent('BLOB3');



        $repository->flush();

        $queryTable = $this->getConnection()->createQueryTable(
            'mail_attachment_content', 'SELECT * FROM mail_attachment_content'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/message.xml'
        )->getTable("mail_attachment_content");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

}
