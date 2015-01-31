<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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

    /**
     * @param \Conjoon\Mail\Client\Message\MessageLocation $messageLocation
     * @param \Conjoon\User\User $user
     *
     *
     * @return ProtocolResult
     *
     * @throws ProtocolException
     */
    public function getMessage(
        \Conjoon\Mail\Client\Message\MessageLocation $messageLocation,
        \Conjoon\User\User $user);

    /**
     * @param \Conjoon\Mail\Client\Message\AttachmentLocation $attachmentLocation
     * @param \Conjoon\User\User $user
     *
     *
     * @return ProtocolResult
     *
     * @throws ProtocolException
     */
    public function getAttachment(
        \Conjoon\Mail\Client\Message\AttachmentLocation $attachmentLocation,
        \Conjoon\User\User $user);

}