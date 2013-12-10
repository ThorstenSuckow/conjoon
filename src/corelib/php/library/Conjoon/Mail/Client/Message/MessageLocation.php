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


namespace Conjoon\Mail\Client\Message;

/**
 * Encapsulates information about the location of an email message. A
 * MessageLocation provides information about the folder the message can be
 * found in, and the id of the message itself..
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MessageLocation {

    /**
     * Returns the folder where the message can be found in.
     *
     * @return \Conjoon\Mail\Client\Folder\Folder
     */
    public function getFolder();

    /**
     * Returns the id of the message.
     *
     * @return mixed
     */
    public function getUId();

    /**
     * Returns a string representation of this MessageLocation.
     * @return string
     */
    public function __toString();

}

