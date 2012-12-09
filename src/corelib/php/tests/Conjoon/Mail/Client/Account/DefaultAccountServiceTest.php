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

namespace Conjoon\Mail\Client\Account;

/**
 * @see Conjoon\Mail\Client\Account\DefaultAccountService
 */
require_once 'Conjoon/Mail/Client/Account/DefaultAccountService.php';

/**
 * @package Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAccountServiceTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $service;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/account.xml'
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->service = new DefaultAccountService(
            array(
                'user'          => new UserMock(),
                'folderService' => new \Conjoon\Mail\Client\Folder\DefaultFolderService(array(
                    'user'                 => new UserMock(),
                    'mailFolderCommons'    => new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(
                        array(
                        'user' => new UserMock(),
                        'mailFolderRepository' => $this->_entityManager->getRepository(
                            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity'
                        ))
                    ),
                    'mailFolderRepository' => $this->_entityManager->getRepository(
                        '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity'
        )))));

    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testContructor_Exception()
    {
        new DefaultAccountService(array(
            'user' => new UserMock()
        ));
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Account\AccountServiceException
     */
    public function testGetMailAccountToAccessRemoteFolder_Exception()
    {
        $this->service->getMailAccountToAccessRemoteFolder(
            new \Conjoon\Mail\Client\Folder\Folder(
                new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                    '["root", "3", "4"]'
        )));
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Account\AccountServiceException
     */
    public function testGetMailAccountToAccessRemoteFolder_Exception2()
    {
        $this->service->getMailAccountToAccessRemoteFolder(
            new \Conjoon\Mail\Client\Folder\Folder(
                new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                    '["root", "fgdgf", "fdfdf"]'
                )));
    }

    /**
     * Ensure everything works as expected.
     */
    public function testGetMailAccountToAccessRemoteFolder_Ok()
    {
        $this->assertNull($this->service->getMailAccountToAccessRemoteFolder(
            new \Conjoon\Mail\Client\Folder\Folder(
                new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                    '["root", "5", "6"]'
                ))));

        $this->assertSame(
            1,
            $this->service->getMailAccountToAccessRemoteFolder(
            new \Conjoon\Mail\Client\Folder\Folder(
                new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                    '["root", "1", "2"]'
                )))->getId());

        $this->assertSame(
            1,
            $this->service->getMailAccountToAccessRemoteFolder(
                new \Conjoon\Mail\Client\Folder\Folder(
                    new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                        '["root", "1"]'
                    )))->getId());
    }

}

class UserMock implements \Conjoon\User\User {

    public function getId(){}

    public function getFirstname(){}

    public function getLastname(){}

    public function getEmailAddress(){}

    public function getUserName(){}

}