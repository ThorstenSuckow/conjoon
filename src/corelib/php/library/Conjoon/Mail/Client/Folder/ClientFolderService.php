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

namespace Conjoon\Mail\Client\Folder;

/**
 * @see Conjoon_Mail_Client_Folder_ClientMailboxFolder
 */
require_once 'Conjoon/Mail/Client/Folder/ClientMailboxFolder.php';


/**
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ClientFolderService {


    /**
     * Returns true if the specified folder represents a remote folder,
     * otherwise false.
     *
     * @param Conjoon_Mail_Client_Folder_ClientMailboxFolder $folder
     *
     * @return boolean
     *
     * @throws ClientMailFolderServiceException
     */
    public function isClientMailboxFolderRepresentingRemoteMailbox(
        \Conjoon_Mail_Client_Folder_ClientMailboxFolder $folder);


}