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

class Conjoon_Modules_Groupware_Email_Message_Dto extends Conjoon_Dto {

    public $id;
    public $uId;
    public $path;
    public $to;
    public $cc;
    public $bcc;
    public $replyTo;
    public $from;
    public $subject;
    public $date;
    public $isSpam;
    public $isPlainText;
    public $body;
    public $attachments;
    public $groupwareEmailFoldersId;

}
