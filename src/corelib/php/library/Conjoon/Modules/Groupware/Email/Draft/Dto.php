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

class Conjoon_Modules_Groupware_Email_Draft_Dto extends Conjoon_Dto {

    public $id;
    public $groupwareEmailFoldersId;
    public $groupwareEmailAccountsId;
    public $subject;
    public $inReplyTo;
    public $references;
    public $date;
    public $contentTextPlain;
    public $contentTextHtml;
    public $to;
    public $cc;
    public $bcc;
    public $attachments;
    public $referencedData;
    public $path;

}