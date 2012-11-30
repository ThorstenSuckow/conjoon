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


namespace Conjoon\Mail\Client\Folder;

/**
 * @see Conjoon\Mail\Client\Service\DefaultClientMailFolderService
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultClientMailFolderService.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @see Conjoon\Mail\Client\Folder\DefaultClientMailFolderPath
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultClientMailFolderPath.php';

/**
 * @see Conjoon\Mail\Client\Folder\MailFolder
 */
require_once 'Conjoon/Mail/Client/Folder/MailFolder.php';

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
class DefaultClientMailFolderServiceTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $clientMailFolderFail;

    protected $clientMailFolder;

    protected $clientMailFolderNoRemote;

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
            new MailFolder(
                new DefaultClientMailFolderPath(
                    '["root", "2", "INBOXtttt", "rfwe2", "New folder (7)"]'
                )
            );

        $this->clientMailFolder =
            new MailFolder(
                new DefaultClientMailFolderPath(
                    '["root", "1", "INBOXtttt", "rfwe2", "New folder (7)"]'
                )
            );

        $this->clientMailFolderFail =
            new MailFolder(
                new DefaultClientMailFolderPath(
                    '["root", "ettwe2e", "INBOXtttt", "rfwe2", "New folder (7)"]'
                )
            );

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');


        $this->service = new DefaultClientMailFolderService(array(
            'mailFolderRepository' => $repository,
            'user'                 => $this->user,
            'mailFolderCommons'    =>
                new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(
                    array(
                        'mailFolderRepository' => $repository,
                        'user'                 => $this->user
                    ))
            ));

        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );
    }

    /**
     * Ensure everything works as expected
     *
     * @expectedException \Conjoon\Mail\Client\Folder\ClientMailFolderServiceException
     */
    public function testFindNone()
    {
        $this->service->isClientMailFolderRepresentingRemoteMailbox(
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
            $this->service->isClientMailFolderRepresentingRemoteMailbox(
                $this->clientMailFolderNoRemote
            )
        );
    }

    /**
     * Ensure everything works as expected
     *
     */
    public function testFind()
    {
        $this->assertSame(
            true,
            $this->service->isClientMailFolderRepresentingRemoteMailbox(
                $this->clientMailFolder
            )
        );
    }

}