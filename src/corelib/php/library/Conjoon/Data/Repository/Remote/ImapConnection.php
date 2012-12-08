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

}