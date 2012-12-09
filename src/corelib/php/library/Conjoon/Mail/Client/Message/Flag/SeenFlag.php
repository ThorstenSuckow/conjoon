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


namespace Conjoon\Mail\Client\Message\Flag;

/**
 * @see Conjoon_Mail_Client_Message_Flag_ClientMessageFlag
 */
require_once 'Conjoon/Mail/Client/Message/Flag/Flag.php';

/**
 * @see Conjoon_Mail_Message_Flag_SeenFlag
 */
require_once 'Conjoon/Mail/Message/Flag/SeenFlag.php';


/**
 * A client message flag is a oo representation of a message flag. A message
 * flag exists of an id for the message, and a boolean value clear which
 * tells whether the flag is about to be set or unset.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SeenFlag extends Flag implements \Conjoon\Mail\Message\Flag\SeenFlag {


    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return '\Seen';
    }

}

