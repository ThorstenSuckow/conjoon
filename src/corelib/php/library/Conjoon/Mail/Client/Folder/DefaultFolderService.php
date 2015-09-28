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
    Conjoon\User\User,
    Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException,
    Conjoon\Mail\Client\Folder\FolderServiceException,
    Conjoon\Mail\Client\Security\FolderAddException,
    Conjoon\Mail\Client\Security\FolderAccessException,
    Conjoon\Mail\Client\Security\FolderMoveException,
    Conjoon\Mail\Client\Folder\FolderMetaInfoMismatchException;

/**
 * @see Conjoon\Mail\Client\Folder\FolderService
 */
require_once 'Conjoon/Mail/Client/Folder/FolderService.php';

/**
 * @see Conjoon\Mail\Client\Security\FolderAccessException
 */
require_once 'Conjoon/Mail/Client/Security/FolderAccessException.php';

/**
 * @see Conjoon\Mail\Client\Folder\FolderMetaInfoMismatchException
 */
require_once 'Conjoon/Mail/Client/Folder/FolderMetaInfoMismatchException.php';

/**
 * @see Conjoon\Mail\Client\Folder\FolderServiceException
 */
require_once 'Conjoon/Mail/Client/Folder/FolderServiceException.php';

/**
 * @see Conjoon\Mail\Client\Security\FolderMoveException
 */
require_once 'Conjoon/Mail/Client/Security/FolderMoveException.php';

/**
 * @see Conjoon\Mail\Client\Security\FolderAddException
 */
require_once 'Conjoon/Mail/Client/Security/FolderAddException.php';

/**
 * @see Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DoctrineMailFolderRepository.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';


/**
 * Default implementation of ClientFolderService
 *
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @uses ClientFolderService
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFolderService implements FolderService {

    /**
     * @var DoctrineMailFolderRepository
     */
    protected $folderRepository;

    /**
     * @var \Conjoon\User\User
     */
    protected $user;

    /**
     * @var \Conjoon\Mail\Client\Folder\FolderCommons
     */
    protected $mailFolderCommons;

    /**
     * @var \Conjoon\Mail\Client\Security\FolderSecurityService
     */
    protected $folderSecurityService;

    /**
     * @var \Conjoon\Mail\Client\Folder\Strategy\FolderNamingForMovingStrategy
     */
    protected $folderNamingForMovingStrategy;

    /**
     * @inheritdoc
     */
    public function __construct(Array $options)
    {
        $data = array('options' => $options);

        ArgumentCheck::check(array(
            'options' => array(
                'type'       => 'array',
                'allowEmpty' => false
            )
        ), $data);

        if (isset($options['mailFolderRepository'])) {
            throw new \RuntimeException("no repository here since @ticket CN-971");
        }

        ArgumentCheck::check(array(
            'user' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon\User\User'
            ),
            'mailFolderCommons' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon\Mail\Client\Folder\FolderCommons'
            ),
            'folderSecurityService' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon\Mail\Client\Security\FolderSecurityService'
            ),
            'folderNamingForMovingStrategy' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon\Mail\Client\Folder\Strategy\FolderNamingForMovingStrategy'
            )
        ), $options);

        $this->folderSecurityService = $options['folderSecurityService'];

        $this->folderNamingForMovingStrategy =
            $options['folderNamingForMovingStrategy'];

        $this->user              = $options['user'];
        $this->mailFolderCommons = $options['mailFolderCommons'];
    }

    /**
     * @inheritdoc
     */
    public function moveMessages(\Conjoon\Mail\Client\Folder\Folder $sourceFolder,
                                 \Conjoon\Mail\Client\Folder\Folder $targetFolder) {

        try {
            $sourceEntity = $this->mailFolderCommons->getFolderEntity(
                $sourceFolder);
            $targetEntity = $this->mailFolderCommons->getFolderEntity(
                $targetFolder);
        } catch (\Exception $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception.", 0 , $e
            );
        }

        /**
         * @todo remove later on when this works with remote mailboxes
         */
        $isSourceRemote = $this->mailFolderCommons->isFolderRepresentingRemoteMailbox(
            $sourceFolder);
        $isTargetRemote = $this->mailFolderCommons->isFolderRepresentingRemoteMailbox(
            $targetFolder);

        if ($isSourceRemote || $isTargetRemote) {
            throw new \RuntimeException("operation not supported yet.");
        }

        // check access options recursively for sourceFolder first!
        // break here if any folder is found that may not be accessed
        if (!$this->folderSecurityService->isFolderHierarchyAccessible($sourceFolder)) {
            throw new FolderAccessException(
                "Source folder hierarchy is not accessible by user"
            );
        }

        // check access for target folder
        if (!$this->folderSecurityService->isFolderAccessible($targetFolder)) {
            throw new FolderAccessException(
                "Target folder hierarchy is not accessible by user"
            );
        }

        // foldertype mismatch
        // check if type is the same
        if ($sourceEntity->getMetaInfo() !== $targetEntity->getMetaInfo()) {
            throw new FolderMetaInfoMismatchException(
                "Source- and Target-Folder do not share the same type"
            );
        }

        return $this->moveMessagesIntoTargetFolder($sourceEntity, $targetEntity);
    }

    /**
     * Helper function for moveMessages. Recursively moves the messages from
     * all the folders found in $sourceEntity (including $sourceEntity) to
     * $targetEntity.
     *
     * @param \Conjoon\Data\Entity\Mail\FolderEntity $sourceEntity
     * @param \Conjoon\Data\Entity\Mail\FolderEntity $targetEntity
     *
     * @return bool
     */
    protected function moveMessagesIntoTargetFolder(
        \Conjoon\Data\Entity\Mail\MailFolderEntity $sourceEntity,
        \Conjoon\Data\Entity\Mail\MailFolderEntity $targetEntity) {

        $folderCommons = $this->mailFolderCommons;

        $folderCommons->moveMessages($sourceEntity, $targetEntity);

        $childEntities = $folderCommons->getChildFolderEntities($sourceEntity);

        foreach ($childEntities as $childEntity) {
            $this->moveMessagesIntoTargetFolder($childEntity, $targetEntity);
        }

        return true;
    }


    /**
     * @inheritdoc
     */
    public function moveFolder(
        \Conjoon\Mail\Client\Folder\Folder $sourceFolder,
        \Conjoon\Mail\Client\Folder\Folder $targetRootFolder) {

        $sourceEntity = $this->mailFolderCommons->getFolderEntity(
            $sourceFolder);
        $targetEntity = $this->mailFolderCommons->getFolderEntity(
            $targetRootFolder);

        $isSourceRemote = $this->mailFolderCommons->isFolderRepresentingRemoteMailbox(
            $sourceFolder);
        $isTargetRemote = $this->mailFolderCommons->isFolderRepresentingRemoteMailbox(
            $targetRootFolder);


        if ($isSourceRemote || $isTargetRemote) {
            throw new \RuntimeException("operation not supported yet.");
        }

        // check if source may be moved
        if (!$this->folderSecurityService->isFolderMovable($sourceFolder)) {
            throw new FolderMoveException(
                "Source-Folder must not be moved by user"
            );
        }

        // check if security allows adding new folder
        if (!$this->folderSecurityService->mayAppendFolderTo($targetRootFolder)) {
            throw new FolderAddException(
                "User may not add folders to target folder"
            );
        }

        try {
            return $this->mailFolderCommons->moveFolderTo(
                $sourceEntity, $targetEntity,
                $this->getNameForMovingFolder($sourceEntity, $targetEntity)
            );
        } catch (InvalidArgumentException $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception: " .
                $e->getMessage(),
                0, $e
            );
        }
    }

    /**
     * Computes and applies a new folder name based on
     * folderNamingForMovingStrategy for sourceEntity.
     *
     * @param \Conjoon\Data\Entity\Mail\MailFolderEntity $sourceEntity The folder
     *        which might provoce a naming issue
     * @param \Conjoon\Data\Entity\Mail\MailFolderEntity $targetEntity The target
     *        folder which child folders need to be checked against $sourceEntity
     *
     * @return bool
     */
    protected function getNameForMovingFolder(
        \Conjoon\Data\Entity\Mail\MailFolderEntity $sourceEntity,
        \Conjoon\Data\Entity\Mail\MailFolderEntity $targetEntity
    ) {

        $childFolderNames = array();

        $childFolders = $this->mailFolderCommons->getChildFolderEntities(
            $sourceEntity

        );
        foreach ($childFolders as $childFolder) {
            $childFolderNames[] = $childFolder->getName();
        }

        $strategyOptions = array(
            'name' => $sourceEntity->getName(),
            'list' => $childFolderNames
        );

        try {
            $namingResult = $this->folderNamingForMovingStrategy->execute(
                $strategyOptions
            );
        } catch (\Conjoon\Mail\Client\Folder\Strategy\StrategyException $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

        return $namingResult->getName();
    }

    /**
     * @inheritdoc
     */
    public function applyMailAccountsToFolder(
        Array $mailAccounts, \Conjoon\Mail\Client\Folder\Folder $targetFolder) {

        $data = array(
            'accounts' => $mailAccounts,
            'folder'   => $targetFolder
        );

        $config = array(
            'accounts' => array(
                'type'       => 'arrayType',
                'minLength'  => 1,
                'allowEmpty' => false,
                'class'      => '\Conjoon\Data\Entity\Mail\MailAccountEntity'
            ),
            'folder' => array(
                array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Mail\Client\Folder\Folder'
                )
            )
        );

        ArgumentCheck::check($config, $data);

        try {
            $isAccessible =
                $this->folderSecurityService->isFolderHierarchyAccessible($targetFolder);
        } catch (\Conjoon\Mail\Client\Security\FolderSecurityServiceException $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception: " .
                $e->getMessage(), 0, $e
            );
        }

        if (!$isAccessible) {
            throw new \Conjoon\Mail\Client\Security\FolderAccessException(
                "Folder $targetFolder is not accessible by the user."
            );
        }

        return $this->mailFolderCommons->applyMailAccountsToFolder(
            $mailAccounts, $targetFolder
        );

    }


// -------- DEPRECATED API

    /**
     * Returns true if the specified folder represents a remote folder,
     * otherwise false.
     *
     * @param Folder $folder
     *
     * @return boolean
     *
     * @throws FolderServiceException
     *
     * @deprecated use \Conjoon\Mail\Client\FolderFolderCommons::isFolderRepresentingRemoteMailbox
     * instead
     */
    public function isFolderRepresentingRemoteMailbox(Folder $folder) {
        return $this->mailFolderCommons->isFolderRepresentingRemoteMailbox($folder);
    }

    /**
     * Returns true if the specified folder represents an "accounts_root" folder,
     * otherwise false.
     *
     * @param Folder $folder
     *
     * @return boolean
     *
     * @throws FolderServiceException
     *
     * @deprecated use \Conjoon\Mail\Client\FolderFolderCommons::isFolderAccountsRootFolder
     * instead
     */
    public function isFolderAccountsRootFolder(Folder $folder) {
        return $this->mailFolderCommons->isFolderAccountsRootFolder($folder);
    }

    /**
     * Returns true if the specified folder represents a "root" folder,
     * otherwise false.
     *
     * @param Folder $folder
     *
     * @return boolean
     *
     * @throws FolderServiceException
     *
     * @deprecated use \Conjoon\Mail\Client\FolderFolderCommons::isFolderRootFolder
     * instead
     */
    public function isFolderRootFolder(Folder $folder) {
        return $this->mailFolderCommons->isFolderRootFolder($folder);
    }

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
     *
     * @deprecated use \Conjoon\Mail\Client\FolderFolderCommons::getFolderEntity
     * instead
     */
    public function getFolderEntity(Folder $folder) {
        return $this->mailFolderCommons->getFolderEntity($folder);
    }

    /**
     * Returns a list of child folder entities of the specified folder-
     * This method does not check if the read out folder allows for appending
     * child folders.
     * If the folder is not accessible by the current user, an exception is
     * thrown.
     *
     * @param Folder $folder
     *
     * @return array An array of \Conjoon\Data\Entity\Mail\MailFolderEntity
     *               instances; an empty array if no child folders are
     *               available
     *
     * @throws FolderServiceException
     *
     * @deprecated use \Conjoon\Mail\Client\FolderFolderCommons::getChildFolderEntities
     * instead
     */
    public function getChildFolderEntities(Folder $folder) {
        return $this->mailFolderCommons->getChildFolderEntities($folder);
    }

}