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
 * @see Conjoon\Data\Entity\Mail\DefaultMailAccountEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultMailAccountEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailAccountEntityTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected function setUp()
    {
        $this->input = array(
            'name' => 'accountname',
            'address' => 'address',
            'replyAddress' => 'replayaddress',
            'isStandard' => true,
            'protocol' => 'protocol',
            'serverInbox' => 'serverinbox',
            'serverOutbox' => 'serveroutbox',
            'usernameInbox' => 'usernameInbox',
            'usernameOutbox' => 'usernameOutbox',
            'userName' => 'userName',
            'isOutboxAuth' => false,
            'passwordInbox' => 'passwordInbox',
            'passwordOutbox' => 'passwordOutbox',
            'signature' => 'signature',
            'isSignatureUsed' => true,
            'portInbox' => 2323,
            'portOutbox' => 45,
            'inboxConnectionType' => 'SSL',
            'outboxConnectionType' => 'SSL',
            'isCopyLeftOnServer' => true,
            'isDeleted' => false,
            'user' => new \Conjoon\Data\Entity\User\DefaultUserEntity()
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $folder = new DefaultMailAccountEntity();

        foreach ($this->input as $input => $value) {

            $set = 'set' . ucfirst($input);
            $get = 'get' . ucfirst($input);

            $folder->$set($value);

            $this->assertSame($value, $folder->$get());

        }
    }

}