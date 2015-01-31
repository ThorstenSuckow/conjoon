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


namespace Conjoon\Data\Repository\Remote;

/**
 * Interface all ImapAdaptee classes have to implement.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ImapAdaptee  {

    /**
     * Connects to the imap server using the specified connection options and
     * logs the user in.
     *
     * @param array $options An array with connection settings:
     *              - user
     *              - password
     *              - host
     *              - port
     *              - ssl: false, "SSL" or "TLS"
     *
     * @return boolean true if the connection was established
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function connect(array $options);


    /**
     * Selects the specified folder.
     *
     * @param string $path
     *
     * @return string the global name of the selected folder
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function selectFolder($path);

    /**
     * Sets the specified flag for the message or removes it depending on the
     * specified mode.
     *
     * @param string $flag
     * @param mixed  $id
     * @param string $mode '-' to remove the flag, '+' to add the flag
     *
     * @return boolean true if the operation was successfull
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function setFlag($flag, $id, $mode);

    /**
     * Returns the folder delimiter used by the mailbox.
     *
     * @return string
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function getFolderDelimiter();

    /**
     * Returns an array with the raw header and the raw body of the message
     * identified by the unique message id. Returns null if the message was
     * not found.
     *
     * @return array|null
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function getMessage($uId);

    /**
     * Logs the user out and disconnects from the server.
     *
     * @return boolean true if the connection was disconnected
     *
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function disconnect();

    /**
     * Tells whether a connection is established.
     *
     * @return boolean true is a connection is available, otehrwise false.
     */
    public function isConnected();



}