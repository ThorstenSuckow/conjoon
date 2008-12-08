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
 * @see Conjoon_Dto
 */
require_once 'Conjoon/Dto.php';

class Conjoon_Modules_Groupware_Email_Item_Dto extends Conjoon_Dto {

    public $id;
    public $recipients;
    public $sender;
    public $subject;
    public $date;
    public $isRead;
    public $isAttachment;
    public $isSpam;
    public $isDraft;
    public $isOutboxPending;
    public $referencedAsTypes;
    public $groupwareEmailFoldersId;

}