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

namespace Conjoon\Data\Repository\Mail;

/**
 * @see Conjoon\Data\Repository\Mail\ImapMessageFlagRepository
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultMailAccountEntity.php';

class MailAccountMock extends \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity {

    protected $id = 0;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getServerInbox()
    {
        return 'host';
    }

    public function getPortInbox()
    {
        return 143;
    }

    public function getUsernameInbox()
    {
        return 'username';
    }

    public function getPasswordInbox()
    {
        return 'password';
    }

    public function getInboxConnectionType()
    {
        return 'SSL';
    }

}
