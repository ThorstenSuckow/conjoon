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

namespace Conjoon\Data\Entity\Mail;

/**
 * @see Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultMessageFlagEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMessageFlagEntityTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected function setUp()
    {
        $this->input = array(
            'users'               => new \Conjoon\Data\Entity\User\DefaultUserEntity,
            'groupwareEmailItems' => new \Conjoon\Data\Entity\Mail\DefaultMessageEntity,
            'isRead'              => false,
            'isDeleted'           => false,
            'isSpam'              => true
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $user = new DefaultMessageFlagEntity();

        foreach ($this->input as $field => $value) {
            $methodSet = "set" . ucfirst($field);
            $methodGet = "get" . ucfirst($field);
            $user->$methodSet($value);

            $this->assertSame($value, $user->$methodGet());
        }
    }
}