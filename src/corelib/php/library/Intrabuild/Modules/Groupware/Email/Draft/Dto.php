<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Dto.php 68 2008-08-02 13:12:03Z T. Suckow $
 * $Date: 2008-08-02 15:12:03 +0200 (Sa, 02 Aug 2008) $
 * $Revision: 68 $
 * $LastChangedDate: 2008-08-02 15:12:03 +0200 (Sa, 02 Aug 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Modules/Groupware/Email/Message/Dto.php $
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