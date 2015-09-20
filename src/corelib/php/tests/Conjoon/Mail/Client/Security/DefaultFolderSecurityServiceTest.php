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


namespace Conjoon\Mail\Client\Security;

use Conjoon\Mail\Client\Folder\Folder,
    Conjoon\Mail\Client\Folder\DefaultFolderPath;

/**
 * @see DefaultMailFolderSecurityService
 */
require_once 'Conjoon/Mail/Client/Security/DefaultFolderSecurityService.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @see Conjoon_Modules_Default_User
 */
require_once 'Conjoon/Modules/Default/User.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFolderSecurityServiceTest
    extends \Conjoon\DatabaseTestCaseDefault {


    protected $securityService;

    protected $mailFolderOk;

    protected $mailFolderFail;

    protected $mailFolderNotThere;


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

        $user = new \Conjoon\User\AppUser($user);

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');

        $messageRepository =  $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMessageEntity');


        $this->mailFolderOk =
            new Folder(
                new DefaultFolderPath(
                    '["root", "1", "2", "3"]'
                )
            );

        $this->mailFolderFail =
            new Folder(
                new DefaultFolderPath(
                    '["root", "4"]'
                )
            );

        $this->mailFolderNotThere = array(
            new Folder(
                new DefaultFolderPath(
                    '["root", "4323"]'
                )
            ),

            new Folder(
                new DefaultFolderPath(
                    '["root", "4232", "khklkhlhk", "kjllkhlkh"]'
                )
            )
        );

        $this->mailFolderNoChildFolders = array(
            new Folder(
                new DefaultFolderPath(
                    '["root", "1", "14"]'
                )
            )
        );


        $this->securityService = new DefaultFolderSecurityService(array(
            'mailFolderRepository' => $repository,
            'user'                 => $user,
            'mailFolderCommons'    =>
                new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(
                    array(
                        'mailFolderRepository' => $repository,
                        'user'                 => $user,
                        'messageRepository'    => $messageRepository
                ))
        ));


    }

    /**
     * Ensure everything works as expected
     */
    public function testIsFolderHierarchyAccessible_FolderDoesNotExistException_CN958() {

        foreach ($this->mailFolderNotThere as $mailFolderNotThere) {
            $ee = null;
            try {
                $this->securityService->isFolderHierarchyAccessible(
                    $mailFolderNotThere
                );
            } catch (\Exception $e) {
                $ee = $e;
            }

            $this->assertTrue($ee instanceof \Conjoon\Mail\Client\Folder\FolderDoesNotExistException);
        }
    }
    /**
     * Ensures everything works as expected
     */
    public function testIsMailFolderHierarchyAccessible_CN958()
    {

        $this->assertTrue(
            $this->securityService->isFolderHierarchyAccessible(
                new Folder(
                    new DefaultFolderPath(
                        '["root", "1", "2", "3", "15", "16"]'
                    )
                )
            )
        );

        $this->assertFalse(
            $this->securityService->isFolderHierarchyAccessible(
                new Folder(
                    new DefaultFolderPath(
                        '["root", "1", "2", "3", "15"]'
                    )
                )
            )
        );

        $this->assertFalse(
            $this->securityService->isFolderHierarchyAccessible(
                $this->mailFolderOk
            )
        );

        $this->assertFalse(
            $this->securityService->isFolderHierarchyAccessible(
                $this->mailFolderFail
            )
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testIsMailFolderHierarchyAccessibleForRemote_CN958()
    {

        $this->assertTrue(
            $this->securityService->isFolderHierarchyAccessible(
                new Folder(
                    new DefaultFolderPath(
                        '["root", "123", "2432432", "3253532253"]'
                    )
                )
            )
        );

        $this->assertFalse(
            $this->securityService->isFolderHierarchyAccessible(
                new Folder(
                    new DefaultFolderPath(
                        '["root", "124", "2432432", "3253532253"]'
                    )
                )
            )
        );
    }


    /**
     * Ensure everything works as expected
     */
    public function testMayAppendFolderTo_NoChildFoldersAllowedException() {
        foreach ($this->mailFolderNoChildFolders as $mailFolderNoChildFolders) {
            $ee = null;
            try {
                $this->securityService->mayAppendFolderTo(
                    $mailFolderNoChildFolders
                );
            } catch (\Exception $e) {
                $ee = $e;
            }

            $this->assertTrue($ee instanceof \Conjoon\Mail\Client\Folder\NoChildFoldersAllowedException);
        }
    }

    /**
     * Ensure everything works as expected
     */
    public function testMayAppendFolderTo_FolderDoesNotExistException() {
        foreach ($this->mailFolderNotThere as $mailFolderNotThere) {
            $ee = null;
            try {
                $this->securityService->isFolderMovable(
                    $mailFolderNotThere
                );
            } catch (\Exception $e) {
                $ee = $e;
            }

            $this->assertTrue($ee instanceof \Conjoon\Mail\Client\Folder\FolderDoesNotExistException);
        }
    }

    /**
     * Ensure everything works as expected
     */
    public function testIsFolderMovable_FolderDoesNotExistException() {
        foreach ($this->mailFolderNotThere as $mailFolderNotThere) {
            $ee = null;
            try {
                $this->securityService->isFolderMovable(
                    $mailFolderNotThere
                );
            } catch (\Exception $e) {
                $ee = $e;
            }

            $this->assertTrue($ee instanceof \Conjoon\Mail\Client\Folder\FolderDoesNotExistException);
        }
    }

    /**
     * Ensure everything works as expected
     */
    public function testIsFolderAccessible_FolderDoesNotExistException() {
        foreach ($this->mailFolderNotThere as $mailFolderNotThere) {
            $ee = null;
            try {
                $this->securityService->isFolderAccessible(
                    $mailFolderNotThere
                );
            } catch (\Exception $e) {
                $ee = $e;
            }

            $this->assertTrue($ee instanceof \Conjoon\Mail\Client\Folder\FolderDoesNotExistException);
        }
    }

    /**
     * Ensures everything works as expected
     */
    public function testIsFolderMovable() {
        $this->assertTrue(
            $this->securityService->isFolderMovable(
                $this->mailFolderOk
            )
        );

        $this->assertFalse(
            $this->securityService->isFolderMovable(
                $this->mailFolderFail
            )
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testIsMailFolderAccessible()
    {
        $this->assertTrue(
            $this->securityService->isFolderAccessible(
                $this->mailFolderOk
            )
        );

        $this->assertFalse(
            $this->securityService->isFolderAccessible(
                $this->mailFolderFail
            )
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testIsMailFolderAccessibleForRemote()
    {
        $this->assertTrue(
            $this->securityService->isFolderAccessible(
                new Folder(
                    new DefaultFolderPath(
                        '["root", "1", "2432432", "3253532253"]'
                    )
                )
            )
        );
    }


}
