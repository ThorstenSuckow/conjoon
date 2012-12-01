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
 * A collection of message flags sent from the client.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface FlagCollection {

    /**
     * Creates a new instance.
     *
     * @param string $options A string as sent by the client providing information
     * about the message flags. Implementing classes must take care of parsing
     * this information properly into instances of ClientMessageFlag.
     *
     * @throws Conjoon_Argument_Exception
     * @throws Conjoon_Mail_Client_Message_Flag_ClientMessageFlagException
     */
    public function __construct($options);

    /**
     * Returns the message flags.
     *
     * @return array An array of ClientMessageFlags
     */
    public function getFlags();
}

