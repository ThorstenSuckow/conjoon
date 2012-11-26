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
 * @see Conjoon\Mail\Client\Service\DefaultClientFolderService
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultClientFolderService.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @see Conjoon\Mail\Client\Folder\DefaultClientMailboxFolderPath
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultClientMailboxFolderPath.php';

/**
 * @see Conjoon\Mail\Client\Folder\ClientMailboxFolder
 */
require_once 'Conjoon/Mail/Client/Folder/ClientMailboxFolder.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultClientFolderServiceTest
    extends \Conjoon\DatabaseTestCaseDefault {

    protected $clientMailFolderFail;

    protected $clientMailFolder;

    protected $clientMailFolderNoRemote;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->clientMailFolderNoRemote =
            new \Conjoon_Mail_Client_Folder_ClientMailboxFolder(
                new \Conjoon_Mail_Client_Folder_DefaultClientMailboxFolderPath(
                    '["root", "2", "INBOXtttt", "rfwe2", "New folder (7)"]'
                )
            );

        $this->clientMailFolder =
            new \Conjoon_Mail_Client_Folder_ClientMailboxFolder(
                new \Conjoon_Mail_Client_Folder_DefaultClientMailboxFolderPath(
                    '["root", "1", "INBOXtttt", "rfwe2", "New folder (7)"]'
                )
            );

        $this->clientMailFolderFail =
            new \Conjoon_Mail_Client_Folder_ClientMailboxFolder(
                new \Conjoon_Mail_Client_Folder_DefaultClientMailboxFolderPath(
                    '["root", "ettwe2e", "INBOXtttt", "rfwe2", "New folder (7)"]'
                )
            );
    }

    /**
     * Ensure everything works as expected
     *
     * @expectedException \Conjoon\Mail\Client\Folder\ClientFolderServiceException
     */
    public function testFindNone()
    {

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');


        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );

        $service = new DefaultClientFolderService($repository);

        $service->isClientMailboxFolderRepresentingRemoteMailbox(
            $this->clientMailFolderFail
        );
    }

    /**
     * Ensure everything works as expected
     *
     */
    public function testFindNoRemote()
    {

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');


        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );

        $service = new DefaultClientFolderService($repository);

        $this->assertSame(
            false,
            $service->isClientMailboxFolderRepresentingRemoteMailbox(
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

        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');


        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );

        $service = new DefaultClientFolderService($repository);

        $this->assertSame(
            true,
            $service->isClientMailboxFolderRepresentingRemoteMailbox(
                $this->clientMailFolder
            )
        );
    }
}