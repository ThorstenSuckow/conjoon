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
     * @todo refactor no nee for a contract for the constructor
     *
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
     *                       - folderSecurityService: an instance of
     *                       Conjoon\Mail\Client\Security\FolderSecurityService
     *
     */
    public function __construct(Array $options);

    /**
     * Moves a folder hierarchy represented by the source folder into the folder
     * represented by target.
     * All folders of the source will be moved into the folder of the target
     * with the same type.
     * Implementing classes should make sure that the passed source folder and
     * target older are representing folders with the matching type.
     * Furthermore, security checks should occur for every folder that is
     * being moved/read.
     * The folders get moved for the user which is bound to the current instance
     * of this class.
     * Moved folders need to inherit the account of the target folders. They
     * will not keep their original associations to their mail accounts.
     * Folders may be moved across different accounts. Remote_root Folders must
     * not be moved to root or accounts_root folder hierarchies.
     *
     * @param Folder $sourceFolder The source folder to move
     * @param Folder $targetFolder The target folder where the source folder
     *                             should be appended to.
     *
     *
     * @return \Conjoon\Data\Entity\Mail\MailFolderEntity The updated mail entity
     *         represented by the passed folder
     *
     * @throws \Conjoon\Mail\Client\Folder\FolderServiceException
     * @throws \Conjoon\Mail\Client\Folder\FolderTypeMismatchException
     * @throws \Conjoon\Mail\Client\Folder\NoChildFoldersAllowedException
     * @throws \Conjoon\Mail\Client\Security\FolderMoveException
     * @throws \Conjoon\Mail\Client\Security\FolderAddException
     */
    public function moveFolder(
        \Conjoon\Mail\Client\Folder\Folder $sourceFolder,
        \Conjoon\Mail\Client\Folder\Folder $targetRootFolder);


    /**
     * Recursively moves all the messages found in $sourceFolder and its child
     * folders to $targetFolder.
     * The folder meta info of $sourceFolder and $targetFolder must be equal.
     * Implementing classes must check if the messages found in all the folders
     * found within $sourceFolder are accessible, and let the operation void if
     * this is not the case.
     *
     * @param Folder $sourceFolder The source folder to read the messages from
     * @param Folder $targetFolder The target folder where the messages should
     *                             be moved to
     *
     * @return boolean true
     *
     * @throws \Conjoon\Mail\Client\Folder\FolderServiceException
     * @throws \Conjoon\Mail\Client\Folder\FolderMetaInfoMismatchException
     * @throws \Conjoon\Mail\Client\Security\FolderAccessException
     */
    public function moveMessages(\Conjoon\Mail\Client\Folder\Folder $sourceFolder,
                                 \Conjoon\Mail\Client\Folder\Folder $targetFolder);

    /**
     * Will apply all the mail accounts found in $mailAccounts to the specified
     * target folder and it's child folders.
     *
     * @param Array $mailAccounts An array of instances of
     *        \Conjoon\Mail\Client\Account\Account
     * @param Folder $targetFolder The target folder to which the mail
     *        accounts get applied.
     *
     * @return \Conjoon\Data\Entity\Mail\MailFolderEntity The updated folder entity
     *         with the merged accounts
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     * @throws \Conjoon\Mail\Client\Folder\IllegalFolderRootTypeException
     * @throws \Conjoon\Mail\Client\Folder\FolderOperationProtocolSupportException
     * @throws \Conjoon\Mail\Client\Security\FolderAccessException
     * @throws \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    public function applyMailAccountsToFolder(
        Array $mailAccounts, \Conjoon\Mail\Client\Folder\Folder $targetFolder);
}