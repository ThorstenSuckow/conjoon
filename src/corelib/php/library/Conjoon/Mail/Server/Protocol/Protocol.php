<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

    /**
     * Returns the attachment for the specified message.
     *
     * @param array $options an array of options for this method:
     *        - user: an instance of \Conjoon\User\User
     *        - parameters: the list of parameters the original request was called
     *        with, containing the key
     *              - attachmentLocation which holds an instance of
     *              \Conjoon\Mail\Client\Messagge\AttachmentLocation
     *
     * @return \Conjoon\Mail\Server\Protocol\ProtocolResult
     */
    public function getAttachment(array $options);

}