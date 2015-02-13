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


namespace Conjoon\Mail\Client\Folder;

use Conjoon\Data\Repository\Mail\MailFolderRepository,
    Conjoon\User\User;

/**
 * @see \Conjoon\Mail\Client\Folder\Folder
 */
require_once 'Conjoon/Mail/Client/Folder/Folder.php';


/**
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface FolderService {

    /**
     * Creates a new instance of a folder service.
     * A folder service is bound to a user.
     *
     * @param array $options An array with instances of MailFolderRepository,
     *                       and a User to use.
     *                       - user: and instance of \Conjoon\User\User
     *                       - mailFolderRepository: an instance of
     *                       Conjoon\Data\Repository\Mail\MailFolderRepository
     *                       - mailFolderCommons: an instance of
     *                       Conjoon\Mail\Client\Folder\FolderCommons
     *
     */
    public function __construct(Array $options);

    /**
     * Returns true if the specified folder represents a remote folder,
     * otherwise false.
     *
     * @param Folder $folder
     *
     * @return boolean
     *
     * @throws FolderServiceException
     */
    public function isFolderRepresentingRemoteMailbox(Folder $folder);

    /**
     * Returns true if the specified folder represents an "accounts_root" folder,
     * otherwise false.
     *
     * @param Folder $folder
     *
     * @return boolean
     *
     * @throws FolderServiceException
     */
    public function isFolderAccountsRootFolder(Folder $folder);

    /**
     * Returns true if the specified folder represents a "root" folder,
     * otherwise false.
     *
     * @param Folder $folder
     *
     * @return boolean
     *
     * @throws FolderServiceException
     */
    public function isFolderRootFolder(Folder $folder);

    /**
     * Returns the folder entity for the secified folder. The repository used
     * for retrieving the entity is the repository configured for an instance of
     * this class.
     * Note: If the specified folder represents a remote mailbox, the id passed
     * int he folder might not exist in the local data storage. If this is the
     * case, null will be returned.
     *
     * @param Folder $folder
     *
     * @return \Conjoon\Data\Entity\Mail\MailFodlerEntity|null
     *
     * @throws FolderServiceException
     */
    public function getFolderEntity(Folder $folder);

}