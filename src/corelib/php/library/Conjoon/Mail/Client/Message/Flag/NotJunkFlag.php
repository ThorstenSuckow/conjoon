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
 * @see Conjoon\Mail\Client\Message\Flag\Flag
 */
require_once 'Conjoon/Mail/Client/Message/Flag/Flag.php';

/**
 * @see Conjoon\Mail\Message\Flag\NotJunkFlag
 */
require_once 'Conjoon/Mail/Message/Flag/NotJunkFlag.php';


/**
 * A flag implementation representing a "NotJunk" message flag.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class NotJunkFlag extends Flag implements \Conjoon\Mail\Message\Flag\NotJunkFlag {


    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return '$NotJunk';
    }

}

