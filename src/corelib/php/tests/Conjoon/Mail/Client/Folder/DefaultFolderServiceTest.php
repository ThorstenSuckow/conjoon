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
 * @see Conjoon\Mail\Client\Folder\Strategy\DefaultFolderNamingForMovingStrategy
 */
require_once 'Conjoon/Mail/Client/Folder/Strategy/DefaultFolderNamingForMovingStrategy.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultClientMailFolderServiceTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $clientMailFolderFail;

    protected $clientMailFolder;

    protected $clientMailFolderNoRemote;

    protected $rootMailFolder;

    protected $accountsRootMailFolder;

    protected $user;

    protected $userAccessibleFail;

    protected $service;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $user = new \Conjoon_Modules_Default_User();
        $user->setId(1);
        $user->setFirstName("f");
        $user->setLastName("l");
        $user->setUsername("u");
        $user->setEmailAddress("ea");

        $this->user = new \Conjoon\User\AppUser($user);

        $user->setId(2);
        $this->userAccessibleFail = new \Conjoon\User\AppUser($user);


        $this->clientMailFolderNoRemote =
            new Folder(
                new DefaultFolderPath(
                    '["root", "2", "INBOXtttt", "rfwe2", "New folder (7)"]'
                )
            );

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

        $this->clientMailFolderFail =
            new Folder(
                new DefaultFolderPath(
                    '["root", "ettwe2e", "INBOXtttt", "rfwe2", "New folder (7)"]'
                )
            );

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $mailFolderCommons = new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(
            array(
                'mailFolderRepository' => $repository,
                'user'                 => $this->user
            ));

        $folderSecurityService =
            new \Conjoon\Mail\Client\Security\DefaultFolderSecurityService(array(
                'mailFolderRepository' => $repository,
                'user'                 => $this->user,
                'mailFolderCommons'    => $mailFolderCommons
        ));

        $folderNamingForMovingStrategy =
            new \Conjoon\Mail\Client\Folder\Strategy\DefaultFolderNamingForMovingStrategy(
                array('template' => '{0} {1}')
            );

        $this->service = new DefaultFolderService(array(
            'folderSecurityService' => $folderSecurityService,
            'mailFolderRepository' => $repository,
            'user'                 => $this->user,
            'mailFolderCommons'    => $mailFolderCommons,
            'folderNamingForMovingStrategy' => $folderNamingForMovingStrategy

        ));

    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testMoveFolder_FolderServiceException() {
        $folderToMove = new Folder(
            new DefaultFolderPath('["root", "1", "18"]')
        );

        $targetFolder = new Folder(
            new DefaultFolderPath('["root", "4"]')
        );

        $this->service->moveFolder($folderToMove, $targetFolder);
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderTypeMismatchException
     */
    public function testMoveFolder_FolderTypeMismatchException() {
        $folderToMove = new Folder(
            new DefaultFolderPath('["root", "10", "18"]')
        );

        $targetFolder = new Folder(
            new DefaultFolderPath('["root", "14"]')
        );

        $this->service->moveFolder($folderToMove, $targetFolder);
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

        $this->service->moveFolder($folderToMove, $targetFolder);
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Security\FolderMoveException
     */
    public function testMoveFolder_FolderMoveException() {
        $folderToMove = new Folder(
            new DefaultFolderPath('["root", "15", "16"]')
        );

        $targetFolder = new Folder(
            new DefaultFolderPath('["root", "10", "11"]')
        );

        $this->service->moveFolder($folderToMove, $targetFolder);
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Security\FolderAddException
     */
    public function testMoveFolder_FolderAddException() {

        $folderToMove = new Folder(
            new DefaultFolderPath('["root", "10"]')
        );

        $targetFolder = new Folder(
            new DefaultFolderPath('["root", "15"]')
        );

        $this->service->moveFolder($folderToMove, $targetFolder);
    }

    /**
     * Ensure everything works as expected
     */
    public function testMoveFolder() {

        $folderToMove = new Folder(
            new DefaultFolderPath('["root", "10"]')
        );

        $targetFolder = new Folder(
            new DefaultFolderPath('["root", "14"]')
        );

        $this->service->moveFolder($folderToMove, $targetFolder);

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders',
            'SELECT * FROM groupware_email_folders'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.moveFolderResult.xml'
        )->getTable("groupware_email_folders");

        $this->assertTablesEqual($expectedTable, $queryTable);

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders_accounts',
            'SELECT * FROM groupware_email_folders_accounts'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.moveFolderResult.xml'
        )->getTable("groupware_email_folders_accounts");

        $this->assertTablesEqual($expectedTable, $queryTable);

    }


    /**
     * Ensure everything works as expected.
     */
    public function testIsFolderAccountsRootFolder() {
        $this->assertSame(
            true,
            $this->service->isFolderAccountsRootFolder(
                $this->accountsRootMailFolder
            )
        );

        $this->assertSame(
            false,
            $this->service->isFolderAccountsRootFolder(
                $this->clientMailFolder
            )
        );

    }

    /**
     * Ensure everything works as expected.
     */
    public function testIsFolderRootFolder() {
        $this->assertSame(
            true,
            $this->service->isFolderRootFolder(
                $this->rootMailFolder
            )
        );

        $this->assertSame(
            false,
            $this->service->isFolderRootFolder(
                $this->clientMailFolder
            )
        );
    }

    /**
     * Ensure everything works as expected.
     */
    public function testIsFolderRepresentingRootRemoteMailbox() {
        $this->assertSame(
            true,
            $this->service->isFolderRepresentingRemoteMailbox(
                $this->clientMailFolder
            )
        );
    }




    /**
     * Ensure everything works as expected
     *
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testFindNone()
    {
        $this->service->isFolderRepresentingRemoteMailbox(
            $this->clientMailFolderFail
        );
    }

    /**
     * Ensure everything works as expected
     *
     */
    public function testFindNoRemote()
    {
        $this->assertSame(
            false,
            $this->service->isFolderRepresentingRemoteMailbox(
                $this->clientMailFolderNoRemote
            )
        );
    }

    /**
     * Ensures everything works as expected.
     */
    public function testGetFolderEntity()
    {
        $this->assertSame(1,
            $this->service->getFolderEntity(new Folder(
                new DefaultFolderPath('["root", "1"]')
            ))->getId()
        );

        $this->assertSame(2,
            $this->service->getFolderEntity(new Folder(
                new DefaultFolderPath('["root", "1", "2"]')
            ))->getId()
        );


    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testGetFolderEntity_FolderDoesNotExistException() {
        $this->assertNull(
            $this->service->getFolderEntity(new Folder(
                new DefaultFolderPath('["root", "4", "2"]')
            ))
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testGetFolderEntity_FolderDoesNotExistException_2() {
        $this->assertNull(
            $this->service->getFolderEntity(new Folder(
                new DefaultFolderPath('["root", "4334", "22", "2422424", "2424224"]')
            ))
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function testGetFolderEntity_FolderServiceException() {
        $this->assertNull(
            $this->service->getFolderEntity(new Folder(
                new DefaultFolderPath('["root", "sfa", "sdg", "dsgsdg", "sdgsgd"]')
            ))
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    public function testGetFolderEntity_FolderDoesNotExistException_3() {
        $this->assertNull(
            $this->service->getFolderEntity(new Folder(
                new DefaultFolderPath('["root", "1", "22"]')
            ))
        );
    }


}