<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
 * licensing@conjoon.org
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
require_once 'Conjoon/Mail/Client/Security/DefaultMailFolderSecurityService.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailFolderSecurityServiceTest
    extends \Conjoon\DatabaseTestCaseDefault {


    protected $securityService;

    protected $mailFolderOk;

    protected $mailFolderFail;

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

        $this->securityService = new DefaultMailFolderSecurityService(array(
            'mailFolderRepository' => $repository,
            'user'                 => $user,
            'mailFolderCommons'    =>
                new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(
                    array(
                        'mailFolderRepository' => $repository,
                        'user'                 => $user
                ))
        ));


    }

    /**
     * Ensures everything works as expected
     */
    public function testIsMailFolderAccessible()
    {
        $this->assertTrue(
            $this->securityService->isMailFolderAccessible(
                $this->mailFolderOk
            )
        );

        $this->assertFalse(
            $this->securityService->isMailFolderAccessible(
                $this->mailFolderFail
            )
        );

    }

}
