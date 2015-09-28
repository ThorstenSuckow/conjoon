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
 * @see \Conjoon\Mail\Client\Folder\TestFolderMockRepository
 */
require_once 'Conjoon/Mail/Client/Folder/TestFolderMockRepository.php';

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

    protected function getCommons($bUseMock = false) {

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

        return new DefaultFolderCommons(array(
            'messageRepository'    => $this->messageRepository,
            'mailFolderRepository' => $bUseMock !== true
                                      ? $repository
                                      : new TestFolderMockRepository,
            'user'                 => $this->user
        ));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->commons = $this->getCommons();

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

// +--------------------------------------
// | moveFolderTo
// +--------------------------------------
    /**
     * Ensure everything works as expected
     */
    public function testMoveFolder() {

        $folderToMove = new Folder(
            new DefaultFolderPath('["root", "10", "11"]')
        );

        $targetFolder = new Folder(
            new DefaultFolderPath('["root", "14"]')
        );

        $movedEntity = $this->getCommons()->moveFolderTo(
            $folderToMove, $targetFolder
        );

        $this->assertTrue($movedEntity instanceof \Conjoon\Data\Entity\Mail\MailFolderEntity);
        $this->assertEquals("folder 11", $movedEntity->getName());
        $this->assertEquals(14, $movedEntity->getParent()->getId());


        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders',
            'SELECT * FROM groupware_email_folders'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/' .
            'DefaultFolderCommons.moveFolderTo.sameName.xml'
        )->getTable("groupware_email_folders");

        $this->assertTablesEqual($expectedTable, $queryTable);

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders_accounts',
            'SELECT * FROM groupware_email_folders_accounts ORDER BY groupware_email_folders_id'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/' .
            'DefaultFolderCommons.moveFolderTo.sameName.xml'
        )->getTable("groupware_email_folders_accounts");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensure everything works as expected
     */
    public function testMoveFolder_newName() {

        $folderToMove = $this->mailFolderRepository->findById(11);

        $targetFolder = new Folder(
            new DefaultFolderPath('["root", "14"]')
        );

        $movedEntity = $this->getCommons()->moveFolderTo(
            $folderToMove, $targetFolder, "New Name"
        );

        $this->assertTrue($movedEntity instanceof \Conjoon\Data\Entity\Mail\MailFolderEntity);
        $this->assertEquals("New Name", $movedEntity->getName());
        $this->assertEquals(14, $movedEntity->getParent()->getId());


        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders',
            'SELECT * FROM groupware_email_folders'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/' .
            'DefaultFolderCommons.moveFolderTo.newName.xml'
        )->getTable("groupware_email_folders");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testMoveFolder_InvalidArgumentException_newFolderName() {

        $folderToMove = $this->mailFolderRepository->findById(11);
        $targetFolder = new Folder(
            new DefaultFolderPath('["root", "14"]')
        );

        $this->getCommons()->moveFolderTo($folderToMove, $targetFolder, 12);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testMoveFolder_InvalidArgumentException_targetFolder() {

        $folderToMove = new Folder(
            new DefaultFolderPath('["root", "10", "11"]')
        );

        $this->getCommons()->moveFolderTo($folderToMove, 2323, null);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testMoveFolder_InvalidArgumentException_folderToMove() {

        $targetFolder = new Folder(
            new DefaultFolderPath('["root", "10", "17"]')
        );

        $this->getCommons()->moveFolderTo('a', $targetFolder, null);
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testMoveFolder_FolderDoesNotExistException() {

        $folderToMove = new Folder(
            new DefaultFolderPath('["root", "10", "123"]')
        );

        $targetFolder = new Folder(
            new DefaultFolderPath('["root", "10", "17"]')
        );

        $this->getCommons(true)->moveFolderTo($folderToMove, $targetFolder);
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testMoveFolder_FolderServiceException() {

        $folderToMove = $this->mailFolderRepository->findById(11);
        $targetFolder = $this->mailFolderRepository->findById(14);

        $this->getCommons(true)->moveFolderTo($folderToMove, $targetFolder);
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderMetaInfoMismatchException
     */
    public function testMoveFolder_FolderMetaInfoMismatchException() {
        $folderToMove = $this->mailFolderRepository->findById(18);
        $targetFolder = $this->mailFolderRepository->findById(14);

        $this->getCommons()->moveFolderTo($folderToMove, $targetFolder, 'META');
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\NoChildFoldersAllowedException
     */
    public function testMoveFolder_NoChildFoldersAllowedException() {
        $folderToMove = new Folder(
            new DefaultFolderPath('["root", "10", "11"]')
        );

        $targetFolder = new Folder(
            new DefaultFolderPath('["root", "10", "17"]')
        );

        $this->getCommons()->moveFolderTo($folderToMove, $targetFolder);
    }

// +--------------------------------------
// | isMetaInfoInFolderHierarchyUnique
// +--------------------------------------
    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testIsMetaInfoInFolderHierarchyUnique_FolderServiceException() {
        $folder = $this->mailFolderRepository->findById(11);
        $this->getCommons(true)->isMetaInfoInFolderHierarchyUnique($folder, 'inbox');
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testIsMetaInfoInFolderHierarchyUnique_FolderDoesNotExistException() {
        $folder = new Folder(new DefaultFolderPath('["root", "123"]'));
        $this->getCommons()->isMetaInfoInFolderHierarchyUnique($folder);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testIsMetaInfoInFolderHierarchyUnique_InvalidArgumentException_1() {
        $this->getCommons()->isMetaInfoInFolderHierarchyUnique('a');
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testIsMetaInfoInFolderHierarchyUnique_InvalidArgumentException_2() {
        $folder = $this->mailFolderRepository->findById(11);
        $this->getCommons()->isMetaInfoInFolderHierarchyUnique($folder, 2);
    }

    /**
     * Ensure everything works as expected.
     */
    public function testIsMetaInfoInFolderHierarchyUnique() {

        $folder = $this->mailFolderRepository->findById(11);
        $this->assertTrue(
            $this->getCommons()->isMetaInfoInFolderHierarchyUnique($folder)
        );
        $folder = new Folder(new DefaultFolderPath('["root", "10", "11"]'));
        $this->assertTrue(
            $this->getCommons()->isMetaInfoInFolderHierarchyUnique($folder, "")
        );
        $folder = $this->mailFolderRepository->findById(11);
        $this->assertFalse(
            $this->getCommons()->isMetaInfoInFolderHierarchyUnique($folder, " ")
        );
        $folder = $this->mailFolderRepository->findById(11);
        $this->assertTrue(
            $this->getCommons()->isMetaInfoInFolderHierarchyUnique($folder, null)
        );

        $folder = $this->mailFolderRepository->findById(11);
        $this->assertTrue(
            $this->getCommons()->isMetaInfoInFolderHierarchyUnique($folder, 'inbox')
        );

        $folder = $this->mailFolderRepository->findById(10);
        $this->assertFalse(
            $this->getCommons()->isMetaInfoInFolderHierarchyUnique($folder, 'inbox')
        );
        $folder = $this->mailFolderRepository->findById(10);
        $this->assertFalse(
            $this->getCommons()->isMetaInfoInFolderHierarchyUnique($folder)
        );

        $folder = $this->mailFolderRepository->findById(18);
        $this->assertFalse(
            $this->getCommons()->isMetaInfoInFolderHierarchyUnique($folder, 'inbox')
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\IllegalChildFolderTypeException
     */
    public function testApplyTypeToFolder_IllegalChildFolderTypeException() {
        $folder = $this->mailFolderRepository->findById(10);
        $this->getCommons()->applyTypeToFolder('inbox', $folder, true);
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\InvalidFolderTypeException
     */
    public function testApplyTypeToFolder_InvalidFolderTypeException() {
        $folder = $this->mailFolderRepository->findById(10);
        $this->getCommons()->applyTypeToFolder('folde213r', $folder, true);
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testApplyTypeToFolder_FolderServiceException() {
        $folder = $this->mailFolderRepository->findById(10);
        $this->getCommons(true)->applyTypeToFolder('folder', $folder, true);
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testApplyTypeToFolder_FolderDoesNotExistException() {
        $folder = new Folder(new DefaultFolderPath('["root", "123"]'));
        $this->commons->applyTypeToFolder('folder', $folder, true);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testApplyTypeToFolder_InvalidArgumentException_childFolders() {
        $folder = new Folder(new DefaultFolderPath('["root", "10"]'));
        $this->commons->applyTypeToFolder('folder', $folder, 1);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testApplyTypeToFolder_InvalidArgumentException_folder() {
        $this->commons->applyTypeToFolder('folder', '$folder', true);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testApplyTypeToFolder_InvalidArgumentException_type() {
        $folder = new Folder(new DefaultFolderPath('["root", "10"]'));
        $this->commons->applyTypeToFolder(2323, $folder, true);
    }

    /**
     * Ensure everything works as expected.
     */
    public function testApplyTypeToFolder_DefaultFolder_singleFolder() {
        $this->applyToFolderHelper(false, false);
    }

    /**
     * Ensure everything works as expected.
     */
    public function testApplyTypeToFolder_FolderEntity_singleFolder() {
        $this->applyToFolderHelper(true, false);
    }

    /**
     * Ensure everything works as expected.
     */
    public function testApplyTypeToFolder_DefaultFolder() {
        $this->applyToFolderHelper(false, true);
    }

    /**
     * Ensure everything works as expected.
     */
    public function testApplyTypeToFolder_FolderEntity() {
        $this->applyToFolderHelper(true, true);
    }

    /**
     * Helper function for testRemoveMailAccountsFromFolder
     */
    protected function applyToFolderHelper($useEntity = false, $childFolders = true) {

        if ($useEntity === true) {
            $folder = $this->mailFolderRepository->findById(10);
        } else {
            $folder = new Folder(
                new DefaultFolderPath(
                    '["root", "10"]'
                )
            );
        }

        $ret = $this->commons->applyTypeToFolder(
            'folder', $folder, $childFolders
        );

        // groupware_email_folders_accounts
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders', 'SELECT * FROM groupware_email_folders'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) .
            '/fixtures/mysql/mail_folder.applyTypeToFolder_'.
            ($childFolders !== false
             ? 'childFolders'
             : 'singleFolder') .
            '.xml'
        )->getTable("groupware_email_folders");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $this->assertInstanceOf('\Conjoon\Data\Entity\Mail\MailFolderEntity', $ret);

        if ($folder instanceof Folder) {
            $this->assertEquals($ret->getId(), $folder->getRootId());
        } else {
            $this->assertEquals($ret->getId(), $folder->getId());
        }

    }


    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testRemoveMailAccountsFromFolder_FolderServiceException() {
        $this->getCommons(true)->removeMailAccountsFromFolder(
            $this->mailFolderRepository->findById(10)
        );
    }


    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testRemoveMailAccountsFromFolder_FolderDoesNotExistException() {
        $this->commons->removeMailAccountsFromFolder(new Folder(
            new DefaultFolderPath(
                '["root", "123"]'
            )
        ));
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testRemoveMailAccountsFromFolder_InvalidArgumentsException() {
        $this->commons->removeMailAccountsFromFolder('b');
    }

    /**
     * Ensure everything works as expected.
     */
    public function testRemoveMailAccountsFromFolder_FolderEntity() {
        $this->removeMailAccountsFromFolderHelper(true);
    }

    /**
     * Ensure everything works as expected.
     */
    public function testRemoveMailAccountsFromFolder_DefaultFolder() {
        $this->removeMailAccountsFromFolderHelper(false);
    }

    /**
     * Helper function for testRemoveMailAccountsFromFolder
     */
    protected function removeMailAccountsFromFolderHelper($useEntity = false) {

        if ($useEntity === true) {
            $folder = $this->mailFolderRepository->findById(10);
        } else {
            $folder = new Folder(
                new DefaultFolderPath(
                    '["root", "10"]'
                )
            );
        }

        $ret = $this->commons->removeMailAccountsFromFolder($folder);


        // groupware_email_folders_accounts
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders_accounts', 'SELECT * FROM groupware_email_folders_accounts'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.removeMailAccountsFromFolder.xml'
        )->getTable("groupware_email_folders_accounts");
        $this->assertTablesEqual($expectedTable, $queryTable);

        $this->assertInstanceOf('\Conjoon\Data\Entity\Mail\MailFolderEntity', $ret);

        if ($folder instanceof Folder) {
            $this->assertEquals($ret->getId(), $folder->getRootId());
        } else {
            $this->assertEquals($ret->getId(), $folder->getId());
        }

    }

    /**
     * Ensure everything works as expected.
     */
    public function test_CN923_hasMessages()
    {
        $folder1 = new Folder(
            new DefaultFolderPath(
                '["root", "5", "6" ]'
            )
        );

        $folder2 = $this->commons->getFolderEntity(new Folder(
            new DefaultFolderPath(
                '["root", "5", "7" ]'
            )
        ));

        $folder3 = new Folder(
            new DefaultFolderPath(
                '["root", "5", "8" ]'
            )
        );

        $folder4 = $this->commons->getFolderEntity(new Folder(
            new DefaultFolderPath(
                '["root", "14", "19" ]'
            )
        ));

        $this->assertNotNull($folder1);
        $this->assertNotNull($folder2);
        $this->assertNotNull($folder3);
        $this->assertNotNull($folder4);


        $this->assertTrue($this->commons->hasMessages($folder1));
        $this->assertFalse($this->commons->hasMessages($folder2));
        $this->assertTrue($this->commons->hasMessages($folder3));
        $this->assertFalse($this->commons->hasMessages($folder4));

    }

    /**
     * Ensure everything works as expected.
     */
    public function test_CN954()
    {
        $folderGroup = array(
            $this->commons->getFolderEntity(new Folder(
                new DefaultFolderPath(
                    '["root", "5", "6" ]'
                )
            )),
            $this->commons->getFolderEntity(new Folder(
                new DefaultFolderPath(
                    '["root", "5", "7" ]'
                )
            ))
        );

        $this->_CN947($folderGroup);
    }

    /**
     * Ensure everything works as expected.
     */
    public function test_CN947_moveMessages()
    {
        $folderGroup = array(
            new Folder(
                new DefaultFolderPath(
                    '["root", "5", "6" ]'
                )
            ),
            new Folder(
                new DefaultFolderPath(
                    '["root", "5", "7" ]'
                )
            )
        );

        $this->_CN947($folderGroup);
    }

    /**
     * Helper for CN947 related tests
     */
    protected function _CN947($folderGroup)
    {
        $sourceFolder = $folderGroup[0];
        $targetFolder = $folderGroup[1];

        $this->assertNotNull($sourceFolder);
        $this->assertNotNull($targetFolder);

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

        $this->assertSame(3, count($entities));

        $ids = array(6, 7, 8);

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
        $this->assertSame(3, count($entities));
        $ids = array(6, 7, 8);
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
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testDoesFolderAllowChildFolders_InvalidArgumentException() {

        $this->commons->doesFolderAllowChildFolders('folder');

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
