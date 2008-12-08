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

class Intrabuild_Modules_Groupware_Email_Draft_Dto extends Intrabuild_Dto {

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

}