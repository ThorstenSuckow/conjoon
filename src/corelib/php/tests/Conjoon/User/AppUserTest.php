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

namespace Conjoon\User;

/**
 * @see Conjoon_User_AppUser
 */
require_once 'Conjoon/User/AppUser.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class AppUserTest extends \PHPUnit_Framework_TestCase {

    protected $_userData;

    protected $_defaultUser = null;

    protected $_failUser = null;

    protected function setUp()
    {
        $this->_userData = array(
            'id'          => 232323,
            'firstName'   => 'wwrwrw',
            'lastName'    => 'ssfsfsf',
            'username'    => 'fsfsf',
            'emailAddress' => 'fssffssf'
        );

        require_once 'Conjoon/Modules/Default/User.php';

        $this->_defaultUser = new \Conjoon_Modules_Default_User();

        $this->_defaultUser->setId($this->_userData['id']);
        $this->_defaultUser->setFirstName($this->_userData['firstName']);
        $this->_defaultUser->setLastName($this->_userData['lastName']);
        $this->_defaultUser->setEmailAddress($this->_userData['emailAddress']);
        $this->_defaultUser->setUserName($this->_userData['username']);

        $this->_failUser = new \Conjoon_Modules_Default_User();

    }

    /**
     * @expectedException \Conjoon_Argument_Exception
     */
    public function testConstructErrorArgument()
    {
        new AppUser("Bla");
    }

    /**
     * @expectedException \Conjoon\User\UserException
     */
    public function testConstructErrorUserData()
    {
        new AppUser($this->_failUser);
    }

    /**
     * Ensures everythingworks as expected
     */
    public function testConstructOk()
    {
        $appUser = new AppUser($this->_defaultUser);

        $this->assertTrue(is_int($this->_userData['id']));
        $this->assertSame((string)$this->_userData['id'], $appUser->getId());
        $this->assertEquals($this->_userData['firstName'], $appUser->getFirstName());
        $this->assertEquals($this->_userData['lastName'], $appUser->getLastName());
        $this->assertEquals($this->_userData['emailAddress'], $appUser->getEmailAddress());
        $this->assertEquals($this->_userData['username'], $appUser->getUsername());

    }

}
