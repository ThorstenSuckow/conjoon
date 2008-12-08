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
 * @category   Email
 * @package    Conjoon_Modules_Groupware_Email
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
interface Conjoon_Modules_Groupware_Email_Keys
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
