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
 * Interface all RemoteConnection classes have to implement.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface RemoteConnection {


    /**
     * Establishes a connection to the remote repository.
     * Implementing classes should throw an exception if the connection cannot
     * be established, providing detailed information about why the
     * connection could not be established.
     *
     * @param array $options an array of options like user credentials to
     *              establish the connection
     *
     * @return boolean true if the connection was established
     *
     * @throws Conjoon\Argument\InvalidArgumentException
     * @throws RemoteConnectionException
     */
    public function connect(array $options);

    /**
     * Disconnects from the connection.
     *
     * @return boolean true if the connection was disconnected
     *
     * @throws RemoteConnectionException
     */
    public function disconnect();

    /**
     * Returns true if the connection is established, otherwise false.
     *
     * @return bool
     */
    public function isConnected();

}