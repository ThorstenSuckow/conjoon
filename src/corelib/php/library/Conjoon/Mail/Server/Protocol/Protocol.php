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
     * @param array $options an array of options for this method:
     *        - user: an instance of \Conjoon\User\User
     *        - parameters: the list of parameters the original request was called
     *        with, containing the key
     *              - folderFlagCollection which holds an instance of
     *              \Conjoon\Mail\Client\Messagge\Flag\FolderFlagCollection
     *
     * @return \Conjoon\Mail\Server\Protocol\ProtocolResult
     */
    public function setFlags(array $options);

    /**
     * Returns the specified message.
     *
     * @param array $options an array of options for this method:
     *        - user: an instance of \Conjoon\User\User
     *        - parameters: the list of parameters the original request was called
     *        with, containing the key
     *              - messageLocation which holds an instance of
     *              \Conjoon\Mail\Client\Messagge\MessageLocation
     *
     * @return \Conjoon\Mail\Server\Protocol\ProtocolResult
     */
    public function getMessage(array $options);



}