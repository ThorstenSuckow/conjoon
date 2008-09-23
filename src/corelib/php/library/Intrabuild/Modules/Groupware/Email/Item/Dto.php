<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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

class Intrabuild_Modules_Groupware_Email_Item_Dto extends Intrabuild_Dto {

    public $id;
    public $recipients;
    public $sender;
    public $subject;
    public $date;
    public $isRead;
    public $isAttachment;
    public $isSpam;
    public $isDraft;
    public $groupwareEmailFoldersId;

}