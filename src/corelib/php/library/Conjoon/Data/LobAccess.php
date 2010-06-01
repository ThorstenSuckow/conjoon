<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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

/**
 * An interface for objects that gain read/write access to LOBs.
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
interface Conjoon_Data_LobAccess {

    /**
     * Copies a Lob.
     *
     * @param array $data
     *
     * @return mixed a unique identifier for this copied lob, otherwise
     * null
     *
     * @throws Conjoon_Data_Exception
     */
    public function copyLob(Array $data);

    /**
     * Moves a Lob.
     *
     * @param array $data
     *
     * @return mixed a unique identifier for this moved lob, otherwise
     * null
     *
     * @throws Conjoon_Data_Exception
     */
    public function moveLob(Array $data);

    /**
     * Deletes a lob.
     *
     * @param array $data
     *
     * @return bool true on success, otherwise false
     *
     * @throws Conjoon_Data_Exception
     */
    public function deleteLob(Array $data);

    /**
     * Deletes a lob for an id.
     *
     * @param int $id
     *
     * @return bool true on success, otherwise false
     *
     * @throws Conjoon_Data_Exception
     */
    public function deleteLobForId($id);

    /**
     * Sets the name for a lob.
     *
     * @param array $data
     *
     * @return bool true on success, otherwise false
     *
     * @throws Conjoon_Data_Exception
     */
    public function setLobName(Array $data);

    /**
     * Returns the lob's content.
     *
     * @param array $data
     *
     * @return string
     *
     * @throws Conjoon_Data_Exception
     */
    public function getLobContent(Array $data);

    /**
     * Returns the meta data for the lob.
     * If this method returns the lob itself within the returned array,
     * the lob's content must be stored in the property "resource"
     * If the lob's resource should be returned, the property "includeResource"
     * should be available in the passed array.
     *
     * @param array $data
     *
     * @return array or null if not exists
     *
     * @throws Conjoon_Data_Exception
     */
    public function getLobData(Array $data);

    /**
     * Returns a stream resource for a lob.
     *
     * @param array $data
     *
     * @return resource|null
     *
     * @throws Conjoon_Data_Exception
     * @see isStreamAccessSupported
     */
    public function getLobAsStream(Array $data);

    /**
     * Returns true if this class is capable of returning a lob as a
     * stream, otherwise false
     *
     * @return bool
     */
    public function isStreamAccessSupported();

    /**
     * Returns true if this class is capable of writing a lob as a
     * stream, otherwise false
     *
     * @return bool
     */
    public function isStreamWritingSupported();

    /**
     * Saves the lob specified in $data
     *
     * @param array $data
     *
     * @return mixed a unique identifier for this lob, or null
     *
     * @throws Conjoon_Data_Exception
     */
    public function addLob(Array $data);

    /**
     * Adds the lob from a stream.
     *
     * @param array $data
     *
     * @return mixed a unique identifier for this lob, or null
     *
     * @throws Conjoon_Data_Exception
     * @see isStreamWritingSupported
     */
    public function addLobFromStream(Array $data);

}