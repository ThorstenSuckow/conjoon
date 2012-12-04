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


namespace Conjoon\Mail\Server\Request;

/**
 * @see Request
 */
require_once 'Conjoon/Mail/Server/Request/DefaultSetFlagsRequest.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultSetFlagsRequestTest extends \PHPUnit_Framework_TestCase {


    protected $folderFlagCollection;

    protected $user;

    protected $request;

    protected function getFolderFlagCollection()
    {
        $folder = new \Conjoon\Mail\Client\Folder\Folder(
            new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
            )
        );

        $flags =
            new \Conjoon\Mail\Client\Message\Flag\DefaultFlagCollection(
                '[{"id":"173","isRead":false},{"id":"172","isRead":true}]'
            );

        return new \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection(
            $flags, $folder
        );
    }

    protected function getUser()
    {
        $userData = array(
            'id'          => 232323,
            'firstName'   => 'wwrwrw',
            'lastName'    => 'ssfsfsf',
            'username'    => 'fsfsf',
            'emailAddress' => 'fssffssf'
        );

        require_once 'Conjoon/Modules/Default/User.php';

        $defaultUser = new \Conjoon_Modules_Default_User();

        $defaultUser->setId($userData['id']);
        $defaultUser->setFirstName($userData['firstName']);
        $defaultUser->setLastName($userData['lastName']);
        $defaultUser->setEmailAddress($userData['emailAddress']);
        $defaultUser->setUserName($userData['username']);

        return new \Conjoon\User\AppUser($defaultUser);
    }

    protected function getRequest()
    {
        return new DefaultSetFlagsRequest(array(
            'user'       => $this->user,
            'parameters' => array(
                'folderFlagCollection' => $this->folderFlagCollection
            )
        ));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->user = $this->getUser();

        $this->folderFlagCollection = $this->getFolderFlagCollection();
    }

    /**
     * @expectedException Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructWithException()
    {
        new DefaultSetFlagsRequest(array(
            'user'       => $this->user,
            'parameters' => array()
        ));
    }

    /**
     * Ensures everything works as expected
     */
    public function testConstructOk()
    {
        $this->getRequest();
    }

    /**
     * Ensures everything works as expected
     */
    public function testGetProtocolCommand()
    {
        $this->assertSame(
            "setFlags",
            $this->getRequest()->getProtocolCommand()
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testGetUser()
    {
        $this->assertSame(
            $this->user,
            $this->getRequest()->getUser()
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testGetFolderFlagCollection()
    {
        $this->assertSame(
            $this->folderFlagCollection,
            $this->getRequest()->getFolderFlagCollection()
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testGetParameters()
    {
        $params = $this->getRequest()->getParameters();

        $this->assertSame(
            $this->folderFlagCollection,
            $params['folderFlagCollection']
        );
    }
}
