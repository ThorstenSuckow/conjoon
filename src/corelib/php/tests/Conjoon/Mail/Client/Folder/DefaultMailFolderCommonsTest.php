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
 * @see Conjoon\Mail\Client\Service\DefaultMailFolderCommons
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultMailFolderCommons.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailFolderCommonsTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $user;

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
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructWithException()
    {
        new DefaultMailFolderCommons(array('bla' => 'test'));
    }

    /**
     * Ensures everythingworks as expected
     */
    public function testDoesMailFolderExist()
    {
        $repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');


        $commons = new DefaultMailFolderCommons(array(
            'mailFolderRepository' => $repository,
            'user'                 => $this->user
        ));

        $this->assertTrue($commons->doesMailFolderExist(
            new MailFolder(
                new DefaultClientMailFolderPath(
                    '["root", "1", "2"]'
                )
            )
        ));

        $this->assertFalse($commons->doesMailFolderExist(
            new MailFolder(
                new DefaultClientMailFolderPath(
                    '["root", "3", "2"]'
                )
            )
        ));

        $this->assertTrue($commons->doesMailFolderExist(
            new MailFolder(
                new DefaultClientMailFolderPath(
                    '["root", "1"]'
                )
            )
        ));
    }
}
