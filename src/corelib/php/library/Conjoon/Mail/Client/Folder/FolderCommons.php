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

/**
 * A base service for operations on folders and related entities.
 *
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface FolderCommons {

    /**
     * @todo refactor, no contract for constructor needed
     *
     * Creates a new instance of a folder security service.
     * A folder security service is bound to a user.
     *
     * @param array $options An array with instances of MailFolderRepository,
     *                       and a User to use.
     *                       - user: and instance of \Conjoon\User\User
     *                       - mailFolderRepository: an instance of
     *                       Conjoon\Data\Repository\Mail\MailFolderRepository
     *                       - messageRepository: an instance of
     *                       Conjoon\Data\Repository\Mail\MessageRepository
     *
     * @throws \Conjoon\Argument\InvalidArgumentExcpetion
     */
    public function __construct(Array $options);

    /**
     * Returns true if the specified folder exists on client side, otherwise
     * false.
     * This method should return false whenever a client node is not found
     * in the underlying data storage, regardless if the folder exists in a
     * remote repository.
     *
     * @param MailFolder $folder
     *
     * @return boolean
     *
     * @throws FolderServiceException
     */
    public function doesMailFolderExist(Folder $folder);

    /**
     * Returns true if the specified folder allows for adding child folders,
     * otherwise false.
     *
     * @param MailFolder $folder
     *
     * @return boolean
     *
     * @throws FolderServiceException
     * @throws FolderDoesNotExistException
     */
    public function doesFolderAllowChildFolders(Folder $folder);

    /**
     * Returns true if the specified folder represents a remote folder,
     * otherwise false.
     *
     * @param Folder $folder
     *
     * @return boolean
     *
     * @throws FolderServiceException
     * @throws FolderDoesNotExistException
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
     * @throws FolderDoesNotExistException
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
     * @throws FolderDoesNotExistException
     */
    public function isFolderRootFolder(Folder $folder);

    /**
     * Returns the folder entity for the secified folder. The repository used
     * for retrieving the entity is the repository configured for an instance of
     * this class.
     * Note: Implementing classes should be aware of the fact that a remote folder
     * might be submitted where the folder name is parsed as an integer, thus
     * being found in the local data storage and wrongly identified as a local
     * folder. Implementing classes MUST traverse the parent hierarchy to make sure
     * the rootId of the passed folder matches the root id of the root folder
     * of the passed folder.
     *
     * @param Folder $folder
     *
     * @return \Conjoon\Data\Entity\Mail\MailFolderEntity
     *
     * @throws FolderServiceException
     * @throws FolderDoesNotExistException
     */
    public function getFolderEntity(Folder $folder);

    /**
     * Returns a list of child folder entities of the specified folder.
     *
     * @param Folder|\Conjoon\Data\Entity\Mail\MailFolderEntity $folder
     *
     * @return array An array of \Conjoon\Data\Entity\Mail\MailFolderEntity
     *               instances; an empty array if no child folders are
     *               available
     *
     * @throws FolderServiceException
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws FolderDoesNotExistException
     */
    public function getChildFolderEntities($folder);

    /**
     * Moves all the messages from the specified source folder to the specified
     * target folder.
     *
     * @param Folder|\Conjoon\Data\Entity\Mail\MailFolderEntity $sourceFolder
     * @param Folder|\Conjoon\Data\Entity\Mail\MailFolderEntity $targetFolder
     *
     * @throws FolderDoesNotExistException
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function moveMessages($sourceFolder, $targetFolder);

    /**
     * Returns true if the passed Folder or any of its child folders in the
     * representing sub-tree has one or more messages which are not flagged as
     * deleted.
     *
     * @param Folder|\Conjoon\Data\Entity\Mail\MailFolderEntity $folder
     *
     * @return Boolean
     *
     * @throws FolderDoesNotExistException
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function hasMessages($folder);

    /**
     * Applies the mail accounts found in $accounts to $folder and all its
     * child folders.
     * Duplicate account associations will be prevented.
     *
     * @param Array $accounts a collection
     *        of instances of \Conjoon\Data\Entity\Mail\MailAccountEntity
     * @param Folder|\Conjoon\Data\Entity\Mail\MailFolderEntity $folder
     *
     * @return \Conjoon\Data\Entity\Mail\MailFolderEntity The mail folder
     *          entity that was updated
     *
     * @throws FolderDoesNotExistException
     * @throws IllegalFolderRootTypeException
     * @throws FolderOperationProtocolSupportException
     * @throws FolderServiceException
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function applyMailAccountsToFolder(Array $accounts, $folder);

    /**
     * Removes all associations to mail accounts from the specified folder and
     * all its child folders.
     *
     * @param Folder|\Conjoon\Data\Entity\Mail\MailFolderEntity $folder
     *
     * @return \Conjoon\Data\Entity\Mail\MailFolderEntity The mail folder
     *          entity that was updated
     *
     * @throws FolderDoesNotExistException
     * @throws FolderServiceException
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function removeMailAccountsFromFolder($folder);

    /**
     * Applies the specified type to the specified folder. If $childFolders is
     * set to true, child folders will be considered, too.
     * The passed $type will be checked against validity with the FolderTypes
     * found in #FolderTypes.
     *
     * @param string $type
     * @param Folder|\Conjoon\Data\Entity\Mail\MailFolderEntity $targetFolder
     * @param boolean $childFolders
     *
     * @return \Conjoon\Data\Entity\Mail\MailFolderEntity The mail folder
     *          which type was changed.
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws \Conjoon\Mail\Client\Folder\FolderServiceException
     * @throws \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     ** @throws InvalidFolderTypeException
     * @throws IllegalChildFolderTypeException
     *
     * @see FolderTypes
     */
    public function applyTypeToFolder($type, $folder, $childFolders = false);

    /**
     * Checks whether the passed $folder including its child folders share
     * one and the same Meta Info value.
     * Pass $metaInfo as second argument to force the check after this value.
     *
     * @param Folder|\Conjoon\Data\Entity\Mail\MailFolderEntity $targetFolder
     * @param string $metaInfo if $metaInfo is submitted, the $folder has
     *               already be set to this value
     *
     * @return boolean true if one and the same meta info is used throughout
     *                 the folder hierarchy, otherwise false
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws \Conjoon\Mail\Client\Folder\FolderServiceException
     * @throws \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
     public function isMetaInfoInFolderHierarchyUnique($folder, $metaInfo = "");

}