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
     *
     * @expectedException \Doctrine\DBAL\DBALException
     */
    public function testRemove()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity');

        $attachmentContent = $repository->findById(1);

        $repository->remove($attachmentContent);

        $repository->flush();
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
