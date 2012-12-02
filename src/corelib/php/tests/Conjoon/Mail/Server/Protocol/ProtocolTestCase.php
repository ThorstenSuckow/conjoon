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


namespace Conjoon\Mail\Server\Protocol;

/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ProtocolTestCase extends \PHPUnit_Framework_TestCase {


    protected $protocolAdaptee;

    protected $failProtocolAdaptee;

    protected $folderFlagCollection;

    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->protocolAdaptee = new SimpleProtocolAdaptee();

        $this->failProtocolAdaptee = new SimpleProtocolAdaptee(false);

        $folder = new \Conjoon\Mail\Client\Folder\Folder(
            new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
            )
        );

        $flags =
            new \Conjoon\Mail\Client\Message\Flag\DefaultFlagCollection(
                '[{"id":"173","isRead":false},{"id":"172","isRead":true}]'
            );

         $this->folderFlagCollection =
             new \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection(
                $flags, $folder
            );

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

        $this->user = new \Conjoon\User\AppUser($defaultUser);

    }

    public function testDummy()
    {

    }

}
