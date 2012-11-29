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

use Conjoon\Data\Repository\Mail\MailFolderRepository,
    Conjoon\User\User;

/**
 * @see \Conjoon\Mail\Client\Folder\ClientMailFolder
 */
require_once 'Conjoon/Mail/Client/Folder/ClientMailFolder.php';


/**
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ClientMailFolderService {

    /**
     * Creates a new instance of a folder service.
     * A folder service is bound to a user.
     *
     * @param MailFolderRepository $folderRepository The mail folder repository for
     *                                               querying the underlying datastorage
     * @param User                 $user             The user for which the service
     *                                               was created
     *
     */
    public function __construct(MailFolderRepository $folderRepository, User $user);

    /**
     * Checks whether the user bound to this service may access the specified
     * folder.
     * Access means read and write permissions.
     *
     * @param ClientMailFolder $folder The folder
     *                                                                to check
     *
     * @return boolean true on success, false if access is forbidden
     *
     * @throws ClientMailFolderServiceException
     */
    public function isClientMailFolderAccessible(ClientMailFolder $folder);


    /**
     * Returns true if the specified folder represents a remote folder,
     * otherwise false.
     *
     * @param ClientMailFolder $folder
     *
     * @return boolean
     *
     * @throws ClientMailFolderServiceException
     */
    public function isClientMailFolderRepresentingRemoteMailbox(ClientMailFolder $folder);


}