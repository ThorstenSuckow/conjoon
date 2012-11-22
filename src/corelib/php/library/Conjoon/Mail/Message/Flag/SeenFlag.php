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
 * @see Conjoon_Mail_Message_Flag_MessageFlag
 */
require_once 'Conjoon/Mail/Message/Flag/MessageFlag.php';

/**
 * A tagging interface for the "seen" message flag.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Message_Flag
 *
 * @uses Conjoon_Mail_Message_Flag_MessageFlag
 */
interface Conjoon_Mail_Message_Flag_SeenFlag
    extends Conjoon_Mail_Message_Flag_MessageFlag  {

}