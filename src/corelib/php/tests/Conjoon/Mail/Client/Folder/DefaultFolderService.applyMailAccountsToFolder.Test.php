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
 * @see Conjoon\Mail\Client\Security\DefaultFolderSecurityService
 */
require_once 'Conjoon/Mail/Client/Security/DefaultFolderSecurityService.php';

/**
 * @see Conjoon\Mail\Client\Service\DefaultFolderService
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultFolderService.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @see Conjoon\Mail\Client\Folder\DefaultFolderPath
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultFolderPath.php';

/**
 * @see Conjoon\Mail\Client\Folder\Folder
 */
require_once 'Conjoon/Mail/Client/Folder/Folder.php';

/**
 * @see Conjoon_Modules_Default_User
 */
require_once 'Conjoon/Modules/Default/User.php';

/**
 * @see \Conjoon\Mail\Client\Folder\Strategy\DefaultFolderNamingForMovingStrategy
 */
require_once 'Conjoon/Mail/Client/Folder/Strategy/DefaultFolderNamingForMovingStrategy.php';

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
class DefaultFolderService_applyMailAccountsToFolder_Test
    extends \Conjoon\DatabaseTestCaseDefault {

    protected $service;

    protected $baseFileName = 'DefaultFolderCommons';

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/' .
            $this->baseFileName .
            '.applyMailAccountsToFolder.xml'
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->getFolderService();

        $this->accountsToAdd = array(
            $this->accountRepository->findById(2),
            $this->accountRepository->findById(3),
            $this->accountRepository->findById(5)
        );

        $this->rootRemoteFolder =
            new Folder(
                new DefaultFolderPath(
                    '["root", "3"]'
                )
            );

        $this->rootRootFolder = new Folder(
            new DefaultFolderPath(
                '["root", "2", "14", "15"]'
            )
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Security\FolderAccessException
     */
    public function testApplyMailAccountsToFolder_FolderAccessException() {
        $folderService = $this->getFolderService();

        $folderService->applyMailAccountsToFolder(
            $this->accountsToAdd,
            new Folder(
                new DefaultFolderPath(
                    '["root", "1000"]'
                )
            )
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testApplyMailAccountsToFolder_FolderServiceException() {
        $folderService = $this->getFolderService(true);

        $folderService->applyMailAccountsToFolder(
            $this->accountsToAdd,
            new Folder(
                new DefaultFolderPath(
                    '["root", "1"]'
                )
            )
        );

    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\IllegalFolderRootTypeException
     */
    public function testApplyMailAccountsToFolder_IllegalFolderRootTypeException() {
        $folderService = $this->getFolderService();
        $folderService->applyMailAccountsToFolder(
            $this->accountsToAdd, $this->rootRootFolder
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderOperationProtocolSupportException
     */
    public function testApplyMailAccountsToFolder_FolderOperationProtocolSupportException() {
        $folderService = $this->getFolderService();
        $folderService->applyMailAccountsToFolder(
            $this->accountsToAdd, $this->rootRemoteFolder
        );
    }

    /**
     * Ensure everything works as expected.
     */
    public function testApplyMailAccountsToFolder_InvalidArgumentException() {

        $excSum   = 2;
        $excCount = 0;

        $folderService = $this->getFolderService();
        $folder        = new Folder(new DefaultFolderPath('["root", "1"]'));

        // 1
        try {
            $folderService->applyMailAccountsToFolder(array(), $folder);
        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            $excCount++;
        }

        // 2
        try {
            $folderService->applyMailAccountsToFolder(array('sd'), $folder);
        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            $excCount++;
        }

        $this->assertSame($excCount, $excSum);
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testApplyMailAccountsToFolder_FolderDoesNotExistException() {

        $folderService = $this->getFolderService();

        $folderService->applyMailAccountsToFolder(
            $this->accountsToAdd,
            new Folder(new DefaultFolderPath('["root", "123"]'))
        );
    }

    /**
     * Ensure everything works as expected.
     */
    public function testApplyMailAccountsToFolder_withFolder() {

        $folderService = $this->getFolderService();

        $nodeId = 1;

        $accountsToAdd = $this->accountsToAdd;

        $folder = new Folder(
            new DefaultFolderPath(
                '["root", ' . $nodeId . ']'
            )
        );

        $ret = $folderService->applyMailAccountsToFolder(
            $accountsToAdd, $folder);

        $this->assertInstanceof(
            '\Conjoon\Data\Entity\Mail\MailFolderEntity', $ret);

        $this->assertSame($nodeId, $ret->getId());

        // groupware email folders
        $queryTableFolders = $this->getConnection()->createQueryTable(
            'groupware_email_folders', 'SELECT * FROM groupware_email_folders'
        );
        $queryTableRelations = $this->getConnection()->createQueryTable(
            'groupware_email_folders_accounts',
            'SELECT * FROM groupware_email_folders_accounts ORDER BY ' .
            'groupware_email_folders_id, groupware_email_accounts_id'
        );
        $file = dirname(__FILE__) .
            '/fixtures/mysql/' .$this->baseFileName. '.applyMailAccountsToFolder.'.
            'applyMailAccountsToFolder.xml';
        $dataSet = $this->createXmlDataSet($file);
        $expectedTableFolders = $dataSet->getTable("groupware_email_folders");
        $expectedTableRelations = $dataSet->getTable("groupware_email_folders_accounts");

        $this->assertTablesEqual($expectedTableFolders, $queryTableFolders);
        $this->assertTablesEqual($expectedTableRelations, $queryTableRelations);
    }

    /**
     * Helper function for creating DefaultFolderCommons with regular/mocked
     * FolderRepository.
     *
     * @param bool $useSecurityMockRepository true to create a mock repository
     */
    protected function getFolderService($useSecurityMockRepository = false) {

        $this->accountRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');

        $user = new \Conjoon_Modules_Default_User();
        $user->setId(1);
        $user->setFirstName("f");
        $user->setLastName("l");
        $user->setUsername("u");
        $user->setEmailAddress("ea");

        $this->user = new \Conjoon\User\AppUser($user);

        $user = $this->user;

        $messageRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');

        $mailFolderRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');


        $mailFolderCommons = new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(array(
            'messageRepository'    => $messageRepository,
            'mailFolderRepository' =>  $mailFolderRepository,
            'user'                 => $this->user
        ));

        $folderSecurityService =
            new \Conjoon\Mail\Client\Security\DefaultFolderSecurityService(array(
                'mailFolderRepository' => $mailFolderRepository,
                'user'                 => $user,
                'mailFolderCommons'    => new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(array(
                    'messageRepository'    => $messageRepository,
                    'mailFolderRepository' => $mailFolderRepository,
                    'user'                 => $this->user
                )
            )));

        $folderNamingForMovingStrategy =
            new \Conjoon\Mail\Client\Folder\Strategy\DefaultFolderNamingForMovingStrategy(
                array('template' => '{0} {1}')
            );

        return new DefaultFolderService(array(
            'folderSecurityService' => $useSecurityMockRepository !== true
                                       ? $folderSecurityService
                                       : new TestSecurityMockRepository(array()),
            'mailFolderRepository' => $mailFolderRepository,
            'user'                 => $user,
            'mailFolderCommons'    => $mailFolderCommons,
            'folderNamingForMovingStrategy' => $folderNamingForMovingStrategy

        ));

    }

}

class TestSecurityMockRepository
    extends \Conjoon\Mail\Client\Security\DefaultFolderSecurityService {

    public function __construct(array $options) {

    }

    public function isFolderHierarchyAccessible(\Conjoon\Mail\Client\Folder\Folder $folder) {
        throw new \Conjoon\Mail\Client\Security\FolderSecurityServiceException();
    }

}