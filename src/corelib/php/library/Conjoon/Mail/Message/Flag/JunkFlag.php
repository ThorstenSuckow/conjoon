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


namespace Conjoon\Mail\Message\Flag;

/**
 * @see \Conjoon\Mail\Message\Flag\Flag
 */
require_once 'Conjoon/Mail/Message/Flag/Flag.php';

/**
 * A tagging interface for the "junk" message flag.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Message_Flag
 *
 * @uses Conjoon\Mail\Message\Flag\Flag
 */
interface JunkFlag extends Flag  {

}