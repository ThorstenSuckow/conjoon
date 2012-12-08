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