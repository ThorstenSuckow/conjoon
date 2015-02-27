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

namespace Conjoon\Mail\Client\Folder;

/**
 * @see Conjoon\Mail\Client\Service\DefaultFolderCommons
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultFolderCommons.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @see Conjoon_Modules_Default_User
 */
require_once 'Conjoon/Modules/Default/User.php';

/**
 * @see Conjoon\Data\Entity\Mail\DefaultMailFolderEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultMailFolderEntity.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFolderCommonsTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $user;

    protected $commons;

    protected $rootMailFolder;

    protected $accountsRootMailFolder;

    protected $clientMailFolder;

    protected $notExistingFolder;

    protected $argumentExceptionFolder;

    protected $noChildFoldersAlowedFolder;

    protected $childFoldersAllowedFolder;

    protected $mailFolderRepository;

    protected $messageRepository;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');
        $this->mailFolderRepository = $repository;

        $this->messageRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');

        $user = new \Conjoon_Modules_Default_User();
        $user->setId(1);
        $user->setFirstName("f");
        $user->setLastName("l");
        $user->setUsername("u");
        $user->setEmailAddress("ea");

        $this->user = new \Conjoon\User\AppUser($user);

        $this->commons = new DefaultFolderCommons(array(
            'messageRepository'    => $this->messageRepository,
            'mailFolderRepository' => $repository,
            'user'                 => $this->user
        ));

        $this->rootMailFolder =
            new Folder(
                new DefaultFolderPath(
                    '["root", "2"]'
                )
            );

        $this->accountsRootMailFolder =
            new Folder(
                new DefaultFolderPath(
                    '["root", "3"]'
                )
            );

        $this->clientMailFolder =
            new Folder(
                new DefaultFolderPath(
                    '["root", "1", "INBOXtttt", "rfwe2", "New folder (7)"]'
                )
            );

        $this->notExistingFolder = new Folder(
            new DefaultFolderPath(
                '["root", "4"]'
            )
        );

        $this->argumentExceptionFolder = new Folder(
            new DefaultFolderPath(
                '["root", "sdds"]'
            )
        );

        $this->noChildFoldersAllowedFolder = new Folder(
            new DefaultFolderPath(
                '["root", "5", "6" ]'
            )
        );

        $this->childFoldersAllowedFolder = new Folder(
            new DefaultFolderPath(
                '["root", "5", "7" ]'
            )
        );

    }

    /**
     * Ensure everything works as expected.
     */
    public function test_CN947_moveMessages()
    {
        $sourceFolder = new Folder(
            new DefaultFolderPath(
                '["root", "5", "6" ]'
            )
        );

        $targetFolder = new Folder(
            new DefaultFolderPath(
                '["root", "5", "7" ]'
            )
        );

        // groupware email folders
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items', 'SELECT * FROM groupware_email_items'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        )->getTable("groupware_email_items");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $this->commons->moveMessages($sourceFolder, $targetFolder);

        // groupware email folders
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_items', 'SELECT * FROM groupware_email_items'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.moveMessagesResult.xml'
        )->getTable("groupware_email_items");
        $this->assertTablesEqual($expectedTable, $queryTable);

    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function test_CN943_InvalidArgumentException()
    {
        $this->commons->getChildFolderEntities(3);
    }

    /**
     * Ensure everything works as expected
     */
    public function test_CN943_EntityAsArgument()
    {
        $folder = new \Conjoon\Data\Entity\Mail\DefaultMailFolderEntity;
        $folder->setIsChildAllowed(true);
        $this->commons->getChildFolderEntities($folder);

    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructWithException()
    {
        new DefaultFolderCommons(array('bla' => 'test'));
    }

    /**
     * Ensures everything works as expected
     */
    public function testIsFolderRepresentingRemoteMailbox() {
        $this->assertSame(
            true,
            $this->commons->isFolderRepresentingRemoteMailbox(
                $this->clientMailFolder
            )
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testIsFolderRepresentingRemoteMailbox_FolderDoesNotExistException() {
        $this->commons->isFolderRepresentingRemoteMailbox(
            $this->notExistingFolder
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testIsFolderRepresentingRemoteMailbox_FolderServiceException() {
        $this->commons->isFolderRepresentingRemoteMailbox(
            $this->argumentExceptionFolder
        );
    }


    /**
     * Ensures everything works as expected
     */
    public function testIsFolderAccountsRootFolder() {
        $this->assertSame(
            true,
            $this->commons->isFolderAccountsRootFolder(
                $this->accountsRootMailFolder
            )
        );

        $this->assertSame(
            false,
            $this->commons->isFolderAccountsRootFolder(
                $this->clientMailFolder
            )
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testIsFolderAccountsRootFolder_FolderDoesNotExistException() {
        $this->commons->isFolderAccountsRootFolder(
            $this->notExistingFolder
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testIsFolderAccountsRootFolder_FolderServiceException() {
        $this->commons->isFolderAccountsRootFolder(
            $this->argumentExceptionFolder
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testIsFolderRootFolder() {
        $this->assertSame(
            true,
            $this->commons->isFolderRootFolder(
                $this->rootMailFolder
            )
        );

        $this->assertSame(
            false,
            $this->commons->isFolderRootFolder(
                $this->clientMailFolder
            )
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testIsFolderRootFolder_FolderDoesNotExistException() {
        $this->commons->isFolderRootFolder(
            $this->notExistingFolder
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testIsFolderRootFolder_FolderServiceException() {
        $this->commons->isFolderRootFolder(
            $this->argumentExceptionFolder
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testGetFolderEntity_FolderDoesNotExistException() {

        $this->commons->getFolderEntity(new Folder(
            new DefaultFolderPath('["root", "4334", "22", "2422424", "2424224"]')
        ));

    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testGetFolderEntity_ExistingIdNotExistingRootId() {
        $this->commons->getFolderEntity(new Folder(
            new DefaultFolderPath('["root", "4", "2"]')
        ));
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testGetFolderEntity_FolderServiceException() {

        $this->commons->getFolderEntity(new Folder(
            new DefaultFolderPath('["root", "sfa", "sdg", "dsgsdg", "sdgsgd"]')
        ));
    }

    /**
     * Ensures everything works as expected
     */
    public function testGetFolderEntity() {

        $this->assertSame(1,
            $this->commons->getFolderEntity(new Folder(
                new DefaultFolderPath('["root", "1"]')
            ))->getId()
        );

        $this->assertSame(2,
            $this->commons->getFolderEntity(new Folder(
                new DefaultFolderPath('["root", "1", "2"]')
            ))->getId()
        );

    }

    /**
     * Ensures everything works as expected
     */
    public function testGetChildFolderEntities() {

        $entities = $this->commons->getChildFolderEntities(new Folder(
            new DefaultFolderPath('["root", "5"]')
        ));

        $this->assertSame(2, count($entities));

        $ids = array(6, 7);

        foreach ($entities as $entity) {
            $this->assertTrue(in_array($entity->getId(), $ids));
        }

        /**
         * @ticket CN-943
         */
        $entities = $this->commons->getChildFolderEntities(
            $this->commons->getFolderEntity(new Folder(
                new DefaultFolderPath('["root", "5"]')
        )));
        $this->assertSame(2, count($entities));
        $ids = array(6, 7);
        foreach ($entities as $entity) {
            $this->assertTrue(in_array($entity->getId(), $ids));
        }
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testGetChildFolderEntities_FolderServiceException() {
        $this->commons->getChildFolderEntities(
            $this->argumentExceptionFolder
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testGetChildFolderEntities_FolderDoesNotExistException() {
        $this->commons->getChildFolderEntities(
            $this->notExistingFolder
        );
    }

    /**
     * @ticket CN-944
     */
    public function testGetChildFolderEntities_NoChildFoldersAllowedException() {

        try {
            $this->commons->getChildFolderEntities(
                $this->noChildFoldersAllowedFolder
            );
        } catch (\Exception $e) {
            $this->fail("No exception should be thrown");
        }


    }


    /**
     * Ensure everything works as expected.
     */
    public function testDoesFolderAllowChildFolders() {

        $this->assertTrue($this->commons->doesFolderAllowChildFolders(
            $this->childFoldersAllowedFolder
        ));


        $this->assertFalse($this->commons->doesFolderAllowChildFolders(
            $this->noChildFoldersAllowedFolder
        ));

    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testDoesFolderAllowChildFolders_FolderDoesNotExistException() {

        $this->commons->doesFolderAllowChildFolders(
            $this->notExistingFolder
        );

    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testDoesFolderAllowChildFolders_FolderServiceException() {

        $this->commons->doesFolderAllowChildFolders(
            $this->argumentExceptionFolder
        );

    }


    /**
     * Ensures everything works as expected
     */
    public function testDoesMailFolderExist()
    {
        $commons = $this->commons;

        $this->assertTrue($commons->doesMailFolderExist(
            new Folder(
                new DefaultFolderPath(
                    '["root", "1", "2"]'
                )
            )
        ));

        $this->assertFalse($commons->doesMailFolderExist(
            new Folder(
                new DefaultFolderPath(
                    '["root", "1", "2", "3"]'
                )
            )
        ));

        $this->assertFalse($commons->doesMailFolderExist(
            new Folder(
                new DefaultFolderPath(
                    '["root", "0", "2"]'
                )
            )
        ));

        $this->assertFalse($commons->doesMailFolderExist(
            new Folder(
                new DefaultFolderPath(
                    '["root", "3", "2"]'
                )
            )
        ));

        $this->assertTrue($commons->doesMailFolderExist(
            new Folder(
                new DefaultFolderPath(
                    '["root", "1"]'
                )
            )
        ));
    }
}
