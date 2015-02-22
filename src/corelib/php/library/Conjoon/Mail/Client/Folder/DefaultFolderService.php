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
    Conjoon\Mail\Client\Security\FolderAccessException,
    Conjoon\Mail\Client\Security\FolderAddException,
    Conjoon\Mail\Client\Security\FolderMoveException;

/**
 * @see Conjoon\Mail\Client\Folder\FolderService
 */
require_once 'Conjoon/Mail/Client/Folder/FolderService.php';

/**
 * @see Conjoon\Mail\Client\Folder\FolderServiceException
 */
require_once 'Conjoon/Mail/Client/Folder/FolderServiceException.php';

/**
 * @see Conjoon\Mail\Client\Security\FolderAccessException
 */
require_once 'Conjoon/Mail/Client/Security/FolderAccessException.php';

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
     * @var Conjoon\User\User
     */
    protected $user;

    /**
     * @var Conjoon\Mail\Client\Folder\MailFolderCommons
     */
    protected $mailFolderCommons;

    /**
     * @var Conjoon\Mail\Client\Security\FolderSecurityService
     */
    protected $folderSecurityService;

    /**
     * @var Conjoon\Mail\Client\Folder\Strategy\FolderNamingForMovingStrategy
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

        ArgumentCheck::check(array(
            'mailFolderRepository' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon\Data\Repository\Mail\MailFolderRepository'
            ),
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

        $this->folderRepository  = $options['mailFolderRepository'];
        $this->user              = $options['user'];
        $this->mailFolderCommons = $options['mailFolderCommons'];
    }

    /**
     * @inheritdoc
     */
    public function moveFolder(
        \Conjoon\Mail\Client\Folder\Folder $sourceFolder,
        \Conjoon\Mail\Client\Folder\Folder $targetRootFolder) {

        try {
            $sourceEntity = $this->mailFolderCommons->getFolderEntity(
                $sourceFolder);
            $targetEntity = $this->mailFolderCommons->getFolderEntity(
                $targetRootFolder);
        } catch (\Exception $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception.", 0 , $e
            );
        }

        $isSourceRemote = $this->mailFolderCommons->isFolderRepresentingRemoteMailbox(
            $sourceFolder);
        $isTargetRemote = $this->mailFolderCommons->isFolderRepresentingRemoteMailbox(
            $targetRootFolder);


        if ($isSourceRemote || $isTargetRemote) {
            throw new \RuntimeException("operation not supported yet.");
        }

        // check if type is the same
        if ($sourceEntity->getType() !== $targetEntity->getType()) {
            throw new FolderTypeMismatchException(
                "Source- and Target-Folder do not share the same type"
            );
        }

        // check if target folder allows child folder
        if (!$this->mailFolderCommons->doesFolderAllowChildFolders($targetRootFolder)) {
            throw new NoChildFoldersAllowedException(
                "Target-Folder does not allow child folders"
            );
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

        // set new parent for source Folder
        $sourceEntity->setParent($targetEntity);

        $sourceEntity->setName(
            $this->getNameForMovingFolder($sourceEntity, $targetEntity)
        );

        $tmpArr = $targetEntity->getMailAccounts();
        $targetMailAccounts = array();
        if (!is_array($tmpArr) &&
           ($tmpArr instanceof \Doctrine\Common\Collections\Collection)) {
            foreach ($tmpArr as $targetAccount) {
                $targetMailAccounts[] = $targetAccount;
            }
        } else {
            $targetMailAccounts = $tmpArr;
        }

        // set new mail accounts for all the child folders which are belonging
        // to the folder which is moved
        $this->replaceMailAccountsForFolderHierarchy(
            $sourceEntity, $targetMailAccounts, $this->folderRepository);
        try {
            $this->folderRepository->flush();
        } catch (\Exception $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
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
        } catch (\Conjoon\Mail\Client\Message\Folder\StrategyException $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

        return $namingResult->getName();
    }

    /**
     * Replaces all the mail accounts of the specified folder and its subfolders
     * with the accounts found in $targetMailAccounts.
     *
     * @param \Conjoon\Data\Entity\Mail\MailFolderEntity $folder The target folder
     *        of which all mail accounts should be replaced. Child folders will
     *        be considered
     * @param array $targetMailAccounts The mail accounts which should replace
     *        the origin mail accounts of $folder
     *
     *
     */
    protected function replaceMailAccountsForFolderHierarchy(
        \Conjoon\Data\Entity\Mail\MailFolderEntity $folderEntity,
        Array $targetMailAccounts,
        \Conjoon\Data\Repository\Mail\MailFolderRepository $repository) {

        $sourceAccounts = $folderEntity->getMailAccounts();
        $repository->register($folderEntity);

        foreach ($sourceAccounts as $sourceAccount) {
            $folderEntity->removeMailAccount($sourceAccount);
        }

        foreach ($targetMailAccounts as $targetAccount) {
            $folderEntity->addMailAccount($targetAccount);
        }

        $childFolders = $this->mailFolderCommons->getChildFolderEntities(
            $folderEntity
        );

        foreach ($childFolders as $childFolder) {
            $this->replaceMailAccountsForFolderHierarchy(
                $childFolder, $targetMailAccounts, $repository);
        }
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