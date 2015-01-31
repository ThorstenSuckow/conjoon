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
 * Interface all ImapConnection classes have to implement.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ImapConnection extends RemoteConnection {


    /**
     * Selects the folder represented by the specified folder path.
     *
     * @param \Conjoon\Mail\Folder\FolderPath $path
     *
     * @return string the global name of the selected folder
     *
     * @throws ImapConnectionException
     */
    public function selectFolder(\Conjoon\Mail\Folder\FolderPath $path);

    /**
     * Sets the flags as specified in the FlagCollection
     * $NoJunk and $Junk flags deserver special treatment, since they are both
     * mutual exclusive. If the NotJunk flag is set, remove any existing Junk
     * flag. If the Junk flag is set, remove any existing NotJunk flag.
     *
     * @param \Conjoon\Mail\Message\Flag\FlagCollection $flagCollection
     *
     * @return boolean true of the operation was successfull
     *
     * @throws ImapConnectionException
     */
    public function setFlags(\Conjoon\Mail\Message\Flag\FlagCollection $flagCollection);

    /**
     * Returns the folder delimiter for the specified connection.
     *
     * @return string
     *
     * @throws ImapConnectionException
     */
    public function getFolderDelimiter();

    /**
     * Returns the message for the specified message id.
     *
     * @param string $messageId
     *
     * @return array An array with the following key/value pairs:
     *               - header: the raw header of the message
     *               - body: the raw body of the message
     *
     * @throws ImapConnectionException
     */
    public function getMessage($uId);
}