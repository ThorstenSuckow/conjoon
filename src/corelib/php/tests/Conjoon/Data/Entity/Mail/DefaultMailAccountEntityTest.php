<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

    protected $folderMapping1;

    protected $folderMapping2;

    protected function setUp()
    {
        $this->folderMapping1 =
            new \Conjoon\Data\Entity\Mail\DefaultFolderMappingEntity();

        $this->folderMapping2
            = new \Conjoon\Data\Entity\Mail\DefaultFolderMappingEntity();

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
        $account = new DefaultMailAccountEntity();

        foreach ($this->input as $input => $value) {

            $set = 'set' . ucfirst($input);
            $get = 'get' . ucfirst($input);

            $account->$set($value);

            $this->assertSame($value, $account->$get());
        }

        $this->assertSame(0, count($account->getFolderMappings()));

        $this->assertSame($account, $account->addFolderMapping($this->folderMapping1));
        $this->assertSame($account, $account->addFolderMapping($this->folderMapping2));

        $this->assertSame(2, count($account->getFolderMappings()));

        $account->removeFolderMapping($this->folderMapping1);

        $this->assertSame(1, count($account->getFolderMappings()));

        $res = $account->getFolderMappings();


        $this->assertFalse(isset($res[0]));

        $this->assertSame($this->folderMapping2, $res[1]);

    }

}