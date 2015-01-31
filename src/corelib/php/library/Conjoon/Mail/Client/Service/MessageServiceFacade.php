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


namespace Conjoon\Mail\Client\Service;

/**
 * Service facade for operations related to messages.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MessageServiceFacade {

    /**
     * Updates the messages in the specified folder with the specified flag
     * settings.
     *
     * @param mixed $flag Information about the flags which should be set/unset
     * @param mixed $path A path to the folder where the messages which should
     *                    be flagged can be found.
     * @param \Conjoon\User\User $user The user object representing the user
     *                                   who triggered this operation
     *
     *
     * @return ServiceResult
     */
    public function setFlagsForMessagesInFolder($flag, $path,
        \Conjoon\User\User $user);


    /**
     * Returns the message for reading based on the specified id from the
     * specified path for the specified user.
     *
     * @param mixed $id The id of the message that was requested
     * @param mixed $path A path to the folder where the message can be found
     * @param \Conjoon\User\User $user The user object representing the user
     *                                   who triggered this operation
     * @param \Conjoon\Mail\Client\Message\Strategy\ReadableStrategy $readableStrategy
     *  the ReadableStrategy to use for transforming the mailbody into a readable format
     *
     * @return GetMessageServiceResult
     */
    public function getMessage($id, $path, \Conjoon\User\User $user,
        \Conjoon\Mail\Client\Message\Strategy\ReadableStrategy $readableStrategy);

    /**
     * Returns the message with its header fields and its body, but without
     * transforming those values into a readable format.
     *
     * @param mixed $id The id of the message that was requested
     * @param mixed $path A path to the folder where the message can be found
     * @param \Conjoon\User\User $user The user object representing the user
     *                                   who triggered this operation
     *
     *
     * @return GetMessageServiceResult
     */
    public function getUnformattedMessage($id, $path, \Conjoon\User\User $user);

    /**
     * Returns the message for creating a reply.
     *
     * @param mixed $id The id of the message that was requested
     * @param mixed $path A path to the folder where the message can be found
     * @param \Conjoon\User\User $user The user object representing the user
     *                                   who triggered this operation
     * @param boolean $replyAll If true, the message gets prepared to reply
     *                to all recipients of the original message, otherwise just
     *                for the sender of the original message
     *
     * @return GetMessageServiceResult
     */
    public function getMessageForReply(
        $id, $path, \Conjoon\User\User $user, $replyAll = false);

    /**
     * Returns the message for forwarding it.
     *
     * @param mixed $id The id of the message that was requested
     * @param mixed $path A path to the folder where the message can be found
     * @param \Conjoon\User\User $user The user object representing the user
     *                                   who triggered this operation
     *
     *
     * @return GetMessageServiceResult
     */
    public function getMessageForForwarding($id, $path, \Conjoon\User\User $user);

    /**
     * Returns the message for composing it.
     *
     * @param mixed $id The id of the message that was requested
     * @param mixed $path A path to the folder where the message can be found
     * @param \Conjoon\User\User $user The user object representing the user
     *                                   who triggered this operation
     *
     *
     * @return GetMessageServiceResult
     */
    public function getMessageForComposing($id, $path, \Conjoon\User\User $user);

    /**
     * Returns the attachment for downloading.
     * The attachment can be identified by the key of the attachment which
     * usually gets generated by conjoon, the id of the message, and the path to
     * the message.
     *
     * @param mixed $key The key of the attachment
     * @param mixed $uId The id of the message. This is either an
     *              identifier from the local data storage or the identifier of
     *              the mail message as stored on the server
     * @param mixed $path A path to the folder where the message can be found
     * @param \Conjoon\User\User $user The user object representing the user
     *                                   who triggered this operation
     *
     * @return ServiceResult
     */
    public function getAttachment($key, $uId, $path, \Conjoon\User\User $user);

}
