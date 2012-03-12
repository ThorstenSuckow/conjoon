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
    public $isCopyLeftOnServer;

}