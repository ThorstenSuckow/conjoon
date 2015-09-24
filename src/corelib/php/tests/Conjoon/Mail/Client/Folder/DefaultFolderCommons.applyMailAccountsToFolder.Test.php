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
class DefaultFolderCommonsTest_applyMailAccountsToFolder_Test
    extends \Conjoon\DatabaseTestCaseDefault {

    protected $user;

    protected $rootMailFolder;

    protected $mailFolderRepository;

    protected $messageRepository;

    protected $accountRepository;

    protected $accountsToAdd;

    protected $accountsRootFolder;

    protected $rootRemoteFolder;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) .
            '/fixtures/mysql/DefaultFolderCommons.applyMailAccountsToFolder.xml'
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->getCommonService();

        $this->accountsToAdd = array(
            $this->accountRepository->findById(2),
            $this->accountRepository->findById(3),
            $this->accountRepository->findById(5)
        );

        $this->rootMailFolder =
            new Folder(
                new DefaultFolderPath(
                    '["root", "2"]'
                )
            );

        $this->rootRemoteFolder =
            new Folder(
                new DefaultFolderPath(
                    '["root", "3"]'
                )
            );

        $this->rootRootFolder = $this->mailFolderRepository->findById(15);

    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\IllegalFolderRootTypeException
     */
    public function testApplyMailAccountsToFolder_IllegalFolderRootTypeException() {
        $commonService = $this->getCommonService();
        $commonService->applyMailAccountsToFolder(
            $this->accountsToAdd, $this->rootRootFolder
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderOperationProtocolSupportException
     */
    public function testApplyMailAccountsToFolder_FolderOperationProtocolSupportException() {
        $commonService = $this->getCommonService();
        $commonService->applyMailAccountsToFolder(
            $this->accountsToAdd, $this->rootRemoteFolder
        );
    }

    /**
     * Ensure everything works as expected.
     */
    public function testApplyMailAccountsToFolder_InvalidArgumentException() {

        $excSum   = 3;
        $excCount = 0;

        $commonService = $this->getCommonService();
        $folder        = $this->mailFolderRepository->findById(1);

        // 1
        try {
            $commonService->applyMailAccountsToFolder(array(), $folder);
        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            $excCount++;
        }

        // 2
        try {
            $commonService->applyMailAccountsToFolder(array('sd'), $folder);
        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            $excCount++;
        }

        // 3
        try {
            $commonService->applyMailAccountsToFolder(
                $this->accountsToAdd, true);
        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            $excCount++;
        }

        $this->assertSame($excCount, $excSum);
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testApplyMailAccountsToFolder_FolderServiceException() {
        $commonService = $this->getCommonService(true);

        $commonService->applyMailAccountsToFolder(
            $this->accountsToAdd,
            $this->mailFolderRepository->findById(1)
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testApplyMailAccountsToFolder_FolderDoesNotExistException() {

        $commonService = $this->getCommonService();

        $commonService->applyMailAccountsToFolder(
            $this->accountsToAdd,
            new Folder(new DefaultFolderPath('["root", "123"]'))
        );
    }

    /**
     * Ensure everything works as expected.
     */
    public function testApplyMailAccountsToFolder_withFolderEntity() {
        $this->applyMailAccountsToFolder_Helper(true);
    }

    /**
     * Ensure everything works as expected.
     */
    public function testApplyMailAccountsToFolder_withFolder() {
        $this->applyMailAccountsToFolder_Helper(false);
    }


    /**
     * Helper function for applying accounts to a folder.
     * @param bool $useFolderEntity
     */
    protected function applyMailAccountsToFolder_Helper($useFolderEntity = false)
    {
        $commonService = $this->getCommonService();

        $nodeId = 1;

        $accountsToAdd = $this->accountsToAdd;

        if ($useFolderEntity === false) {
            $folder = new Folder(
                new DefaultFolderPath(
                    '["root", ' . $nodeId . ']'
                )
            );
        } else {
            $folder = $this->mailFolderRepository->findById(1);
        }

        $ret = $commonService->applyMailAccountsToFolder($accountsToAdd, $folder);

        $this->assertInstanceof(
            '\Conjoon\Data\Entity\Mail\MailFolderEntity', $ret);

        if ($useFolderEntity === true) {
            $this->assertSame($folder, $ret);
        } else {
            $this->assertSame($ret->getId(), 1);
        }

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
            '/fixtures/mysql/DefaultFolderCommons.applyMailAccountsToFolder.'.
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
     * @param bool $useMockRepository true to create a mock repository
     */
    protected function getCommonService($useMockRepository = false) {
        $this->mailFolderRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $this->messageRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');

        $this->accountRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');

        $user = new \Conjoon_Modules_Default_User();
        $user->setId(1);
        $user->setFirstName("f");
        $user->setLastName("l");
        $user->setUsername("u");
        $user->setEmailAddress("ea");

        $this->user = new \Conjoon\User\AppUser($user);

        return new DefaultFolderCommons(array(
            'messageRepository'    => $this->messageRepository,
            'mailFolderRepository' => $useMockRepository !== true
                                      ? $this->mailFolderRepository
                                      : new \Conjoon\Mail\Client\Folder\TestFolderMockRepository,
            'user'                 => $this->user
        ));
    }
}
