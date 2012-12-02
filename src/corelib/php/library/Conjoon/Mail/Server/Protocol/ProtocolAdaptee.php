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
 * A response interface for providing a default contract for all responses a
 * Server can return.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ProtocolAdaptee {


    /**
     * @param \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $flagCollection
     * @param \Conjoon\User\User $user
     *
     *
     * @return ProtocolResult
     *
     * @throws ProtocolException
     */
    public function setFlags(
        \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $flagCollection,
        \Conjoon\User\User $user);



}