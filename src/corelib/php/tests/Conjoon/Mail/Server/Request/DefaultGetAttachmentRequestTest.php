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
 * @see DefaultGetAttachmentRequest
 */
require_once 'Conjoon/Mail/Server/Request/DefaultGetAttachmentRequest.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultGetAttachmentRequestTest extends \PHPUnit_Framework_TestCase {


    protected $attachmentLocation;

    protected $user;

    protected $request;

    protected function getAttachmentLocation()
    {
        $folder = new \Conjoon\Mail\Client\Folder\Folder(
            new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
            )
        );

        return
            new \Conjoon\Mail\Client\Message\DefaultAttachmentLocation(
                new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                $folder, 1
            ), "1");
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
        return new DefaultGetAttachmentRequest(array(
            'user'       => $this->user,
            'parameters' => array(
                'attachmentLocation' => $this->attachmentLocation
            )
        ));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->user = $this->getUser();

        $this->attachmentLocation = $this->getAttachmentLocation();
    }

    /**
     * @expectedException Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructWithException()
    {
        new DefaultGetAttachmentRequest(array(
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
            "getAttachment",
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
    public function testGetAttachmentLocation()
    {
        $this->assertSame(
            $this->attachmentLocation,
            $this->getRequest()->getParameter('attachmentLocation')
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testGetParameters()
    {
        $params = $this->getRequest()->getParameters();

        $this->assertSame(
            $this->attachmentLocation,
            $params['attachmentLocation']
        );
    }
}
