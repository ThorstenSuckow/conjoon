<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

/**
 * @see Conjoon_Dto
 */
require_once 'Conjoon/Dto.php';

class Conjoon_Modules_Groupware_Email_Account_Dto extends Conjoon_Dto {

    public $id;
    public $name;
    public $address;
    public $replyAddress;
    public $isStandard;
    public $protocol;
    public $serverInbox;
    public $serverOutbox;
    public $usernameInbox;
    public $usernameOutbox;
    public $userName;
    public $isOutboxAuth;
    public $passwordInbox;
    public $passwordOutbox;
    public $signature;
    public $isSignatureUsed;
    public $portInbox;
    public $portOutbox;
    public $inboxConnectionType;
    public $outboxConnectionType;
    public $isCopyLeftOnServer;
    public $folderMappings;

}