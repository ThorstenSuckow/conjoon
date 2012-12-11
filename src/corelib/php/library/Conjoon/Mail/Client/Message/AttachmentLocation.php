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
 * Encapsulates information about the location of an email message attachment.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface AttachmentLocation {

    /**
     * Returns the message location of the message teh attachment belongs to.
     *
     * @return \Conjoon\Mail\Client\Message\AttachmentLocation
     */
    public function getMessageLocation();

    /**
     * Returns the identifier of the attachment.
     *
     * @return mixed
     */
    public function getIdentifier();

}

