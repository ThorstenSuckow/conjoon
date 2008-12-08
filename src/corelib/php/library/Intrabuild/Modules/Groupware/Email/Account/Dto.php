<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * @see Intrabuild_Dto
 */
require_once 'Intrabuild/Dto.php';

class Intrabuild_Modules_Groupware_Email_Account_Dto extends Intrabuild_Dto {

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