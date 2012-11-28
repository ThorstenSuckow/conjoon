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

namespace Conjoon\Data\Entity\User;

/**
 * @see Conjoon\Data\Entity\User\DefaultUserEntity
 */
require_once 'Conjoon/Data/Entity/User/DefaultUserEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultUserEntityTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected function setUp()
    {
        $this->input = array(
            'firstname'    => "name",
            'lastname'     => "Lastname",
            'emailAddress' => "emailAddress",
            'userName'     => "UserName1",
            'password'     => "password",
            'isRoot'       => 1,
            'authToken'    => "authToken",
            'lastLogin'    => 2
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $user = new DefaultUserEntity();

        foreach ($this->input as $field => $value) {
            $methodSet = "set" . ucfirst($field);
            $methodGet = "get" . ucfirst($field);
            $user->$methodSet($value);

            $this->assertSame($value, $user->$methodGet());
        }
    }

}