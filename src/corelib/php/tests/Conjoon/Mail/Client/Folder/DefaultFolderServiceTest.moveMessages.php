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
class DefaultFolderService_moveMessages_Test extends \Conjoon\DatabaseTestCaseDefault {

    protected $service;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/DefaultFolderServiceTest.moveMessages.xml'
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $user = new \Conjoon_Modules_Default_User();
        $user->setId(1);
        $user->setFirstName("firstname");
        $user->setLastName("lastname");
        $user->setUsername("username");
        $user->setEmailAddress("emailaddress");

        $user = new \Conjoon\User\AppUser($user);

        $messageRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $mailFolderCommons = new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(
            array(
                'mailFolderRepository' => $repository,
                'user'                 => $user,
                'messageRepository'    => $messageRepository
            ));

        $folderSecurityService =
            new \Conjoon\Mail\Client\Security\DefaultFolderSecurityService(array(
                'mailFolderRepository' => $repository,
                'user'                 => $user,
                'mailFolderCommons'    => $mailFolderCommons
        ));

        $folderNamingForMovingStrategy =
            new \Conjoon\Mail\Client\Folder\Strategy\DefaultFolderNamingForMovingStrategy(
                array('template' => '{0} {1}')
            );

        $this->service = new DefaultFolderService(array(
            'folderSecurityService' => $folderSecurityService,
            'mailFolderRepository' => $repository,
            'user'                 => $user,
            'mailFolderCommons'    => $mailFolderCommons,
            'folderNamingForMovingStrategy' => $folderNamingForMovingStrategy

        ));

    }

    /**
     * Ensure everything works as expected.
     */
    public function testMoveMessages() {

        $sourceFolder =
            new Folder(
                new DefaultFolderPath(
                    '["root", "1"]'
                )
            );

        $targetFolder =
            new Folder(
                new DefaultFolderPath(
                    '["root", "4", "5"]'
                )
            );

        $this->assertTrue(
            $this->service->moveMessages($sourceFolder, $targetFolder)
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders',
            'SELECT * FROM groupware_email_folders'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) .
            '/fixtures/mysql/DefaultFolderServiceTest.moveMessages.moveMessages.xml'
        )->getTable("groupware_email_folders");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

}