<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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


namespace Conjoon\Mail\Server\Protocol;

/**
 * @see DefaultProtocolAdaptee
 */
require_once 'Conjoon/Mail/Server/Protocol/DefaultProtocolAdaptee.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultProtocolAdapteeTest extends \Conjoon\DatabaseTestCaseDefault {


    protected $messageFlagRepository;

    protected $folderRepository;

    protected $protocolAdaptee;

    protected $folderFlagCollection;

    protected $userRepository;

    protected $mailAccountRepository;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/defaultprotocoladaptee.xml'
        );
    }

    protected function checkforSameDataSet($fileName)
    {
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items_flags',
            'SELECT * FROM groupware_email_items_flags ORDER BY user_id, groupware_email_items_id'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/' . $fileName
        )->getTable("groupware_email_items_flags");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    protected function buildFolderFlagCollection()
    {
        $folder = new \Conjoon\Mail\Client\Folder\Folder(
            new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                '["root", "1"]'
            )
        );

        $flags = new \Conjoon\Mail\Client\Message\Flag\DefaultFlagCollection(
            '[' .
                '{"id":"1","isRead":true},{"id":"2","isRead":true}' .
                ',' .
                '{"id":"3","isRead":true}' .
                ']'
        );

        $this->folderFlagCollection =
            new \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection(
                $flags, $folder
            );

    }

    protected function setUp()
    {
        parent::setUp();

        $this->userRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\User\DefaultUserEntity');

        $this->messageFlagRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity');

        $this->folderRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $this->mailAccountRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');

        $messageRepository =
            $this->_entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMessageEntity');
        $attachmentRepository =
            $this->_entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');

        $this->protocolAdaptee = new DefaultProtocolAdaptee(
            $this->folderRepository, $this->messageFlagRepository,
            $this->mailAccountRepository, $messageRepository, $attachmentRepository

        );

        $this->buildFolderFlagCollection();

        $this->checkForSameDataSet('defaultprotocoladaptee.xml');


    }

    /**
     * Ensures everything works as expected.
     */
    public function testSetFlags()
    {
        $user = $this->userRepository->findById(1);

        $this->protocolAdaptee->setFlags($this->folderFlagCollection, $user);

        $this->checkForSameDataSet('defaultprotocoladaptee.setFlags.result.xml');
    }

    /**
     * @expectedException \Conjoon\Mail\Server\Protocol\ProtocolException
     */
    public function testSetFlagsException()
    {
        $user = $this->userRepository->findById(2);

        $this->protocolAdaptee->setFlags($this->folderFlagCollection, $user);
    }

    /**
     * @expectedException \Conjoon\Mail\Server\Protocol\ProtocolException
     */
    public function testGetMessage()
    {
        $user = $this->userRepository->findById(2);

        $this->protocolAdaptee->getMessage(
            new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                $this->folderFlagCollection->getFolder(), "1"
            ),
            $user
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Server\Protocol\ProtocolException
     */
    public function testGetAttachment()
    {
        $user = $this->userRepository->findById(2);

        $this->protocolAdaptee->getAttachment(
            new \Conjoon\Mail\Client\Message\DefaultAttachmentLocation(
            new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                $this->folderFlagCollection->getFolder(), "1"
            ), "1"
            ),
            $user
        );
    }
}
