<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: SortDirection.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Filter/SortDirection.php $
 */



/**
 * @category   Email
 * @package    Intrabuild_Modules_Groupware_Email
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
interface Intrabuild_Modules_Groupware_Email_Keys
{
    const FORMAT_TEXT_PLAIN  = 'text/plain';
    const FORMAT_TEXT_HTML   = 'text/html';
    const FORMAT_MULTIPART   = 'multipart';

    const REFERENCE_TYPE_NEW       = 'new';
    const REFERENCE_TYPE_EDIT      = 'edit';
    const REFERENCE_TYPE_REPLY     = 'reply';
    const REFERENCE_TYPE_REPLY_ALL = 'reply_all';
    const REFERENCE_TYPE_FORWARD   = 'forward';
}
