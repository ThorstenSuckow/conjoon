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
 * @see Conjoon\Data\Repository\Mail\ImapMessageFlagRepository
 */
require_once 'Conjoon/Data/Repository/Mail/ImapMessageFlagRepository.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ImapMessageFlagRepositoryTest extends \PHPUnit_Framework_TestCase {

    protected $repository;

    protected $mailAccountFail;

    protected $mailAccount;

    protected $mailAccount2;

    protected $folderFlagCollection;

    protected function setUp()
    {
        parent::setUp();

        $this->folderFlagCollection = new \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection(
            new \Conjoon\Mail\Client\Message\Flag\DefaultFlagCollection(
                '[{"id":"173","isRead":false},{"id":"172","isRead":true}]'
            ),
            new \Conjoon\Mail\Client\Folder\Folder(
                new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                    '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
                )
            )
        );

        $this->mailAccountFail =
            new \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity();

        $this->mailAccount =
            new MailAccountMock(1);

        $this->mailAccount2 =
            new MailAccountMock(2);

        $this->repository = new ImapMessageFlagRepository(
            $this->mailAccount,
            array(
                'imapConnectionClassName' =>
                    '\Conjoon\Data\Repository\Remote\DefaultImapConnection',
                'imapAdapteeClassName' =>
                    '\Conjoon\Data\Repository\Remote\SimpleImapAdaptee'
            )
        );
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstruct_Exception()
    {
        new ImapMessageFlagRepository(
            $this->mailAccount,
            array('classLoader' => 'bla')
        );
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testGetConnection_Exception()
    {
        $this->repository->getConnection(array());
    }

    /**
     * Ensures everything works as expected.
     */
    public function testGetEntityClassName_Ok()
    {
        $this->assertSame(
            '\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity',
            $this->repository->getEntityClassName()
        );
    }

    /**
     * Ensures everything works as expected.
     */
    public function testSetFlagsForUser()
    {
        $this->assertTrue(
            $this->repository->setFlagsForUser(
                $this->folderFlagCollection,
                new UserMock
            )
        );
    }

    /**
     * Ensures everything works as expected.
     */
    public function testGetConnection_Ok()
    {
        $conn1 = $this->repository->getConnection(array(
            'mailAccount' => $this->mailAccount
        ));

        $conn2 = $this->repository->getConnection(array(
            'mailAccount' => $this->mailAccount
        ));

        $conn3 = $this->repository->getConnection(array(
            'mailAccount' => $this->mailAccount2
        ));


        $this->assertSame($conn1, $conn2);

        $this->assertNotSame($conn1, $conn3);
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
     * @expectedException \RuntimeException
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

}

class UserMock implements \Conjoon\User\User {

    public function getId(){}

    public function getFirstname(){}

    public function getLastname(){}

    public function getEmailAddress(){}

    public function getUserName(){}

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return
            str_replace(
                array("{id}", "{firstname}", "{lastname}",
                    "{emailAddress}", "{userName}"),
                array($this->getId(), $this->getFirstname(),
                    $this->getLastname(), $this->getEmailAddress(),
                    $this->getUserName()
                ),
                "id:{id};firstname:{firstname};lastname:{lastname};"
                    . "emailAddess:{emailAddress};userName:{userName}]"
            );

    }

}

class MailAccountMock extends \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity {

    protected $id = 0;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getServerInbox()
    {
        return 'host';
    }

    public function getPortInbox()
    {
        return 143;
    }

    public function getUsernameInbox()
    {
        return 'username';
    }

    public function getPasswordInbox()
    {
        return 'password';
    }

    public function getInboxConnectionType()
    {
        return 'SSL';
    }

}
