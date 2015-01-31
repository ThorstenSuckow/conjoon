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
 * @see Conjoon\Data\Repository\Mail\ImapAttachmentRepository
 */
require_once 'Conjoon/Data/Repository/Mail/ImapAttachmentRepository.php';

/**
 * @see \Conjoon\Data\EntityCreator\Mail\SimpleAttachmentEntityCreator
 */
require_once dirname(__FILE__) . '/../../EntityCreator/Mail/SimpleAttachmentEntityCreator.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ImapAttachmentRepositoryTest extends \PHPUnit_Framework_TestCase {

    protected $repository;

    protected $mailAccount;

    protected $attachmentLocation;

    protected function setUp()
    {
        parent::setUp();

        $this->attachmentLocation =
            new \Conjoon\Mail\Client\Message\DefaultAttachmentLocation(
                new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                    new \Conjoon\Mail\Client\Folder\Folder(
                        new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                            '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
                        )
                    ), "1"
                ), "klhlkh"
            );

        $this->mailAccount = new MailAccountMock(1);

        $this->repository = new ImapAttachmentRepository(
            $this->mailAccount,
            array(
                'imapConnectionClassName' =>
                    '\Conjoon\Data\Repository\Remote\DefaultImapConnection',
                'imapAdapteeClassName' =>
                    '\Conjoon\Data\Repository\Remote\SimpleImapAdaptee',
                'attachmentEntityCreatorClassName' => '\Conjoon\Data\EntityCreator\Mail\SimpleAttachmentEntityCreator'
            )
        );
    }

    /**
     * Ensures everything works as expected.
     */
    public function testGetEntityClassName_Ok()
    {
        $this->assertSame(
            '\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity',
            $this->repository->getEntityClassName()
        );
   }

    /**
     * @expectedException \RuntimeException
     */
    public function testPersist_Exception()
    {
        // use mail account as entity, this is okay for now
        $this->repository->register($this->mailAccount);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRemove_Exception()
    {
        // use mail account as entity, this is okay for now
        $this->repository->remove($this->mailAccount);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testFindById_Exception()
    {
        $this->repository->findById(1);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFlush_Exception()
    {
        $this->repository->flush($this->mailAccount);
    }

    /**
     * Ensures everything works as expected.
     */
    public function testFindById()
    {
        $res = $this->repository->findById($this->attachmentLocation);

        $this->assertNull($res);

    }

}
