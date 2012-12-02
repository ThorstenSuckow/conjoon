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


namespace Conjoon\Mail\Server\Protocol;

/**
 * A protocol interface for providing a default contract for the protocol
 * a Conjoon\Mail\Server should be able to handle.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Protocol {



    /**
     * Sets the flags in the specified folder for the specified user.
     * Implementing classes should return OK on success, or ERROR
     * on failure, along with an error description.
     *
     * @param \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $flagCollection
     * @param \Conjoon\User\User $user
     *
     * @return \Conjoon\Mail\Server\Protocol\ProtocolResult
     */
    public function setFlags(
        \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $flagCollection,
        \Conjoon\User\User $user);



}