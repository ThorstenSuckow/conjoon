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

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException;

/**
 * @see MailFolderCommons
 */
require_once 'Conjoon/Mail/Client/Folder/FolderCommons.php';

/**
 * @see FolderDoesNotExistException
 */
require_once 'Conjoon/Mail/Client/Folder/FolderDoesNotExistException.php';

/**
 * @see NoChildFoldersAllowedException
 */
require_once 'Conjoon/Mail/Client/Folder/NoChildFoldersAllowedException.php';

/**
 * @see FolderServiceException
 */
require_once 'Conjoon/Mail/Client/Folder/FolderServiceException.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';



/**
 * A default implementation for FolderCommons.
 *
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFolderCommons implements FolderCommons {

    /**
     * @const ROOT_REMOTE
     */
    const ROOT_REMOTE = 'root_remote';

    /**
     * @const ROOT_REMOTE
     */
    const ACCOUNTS_ROOT = 'accounts_root';

    /**
     * @const ROOT
     */
    const ROOT = 'root';

    /**
     * @var MailFolderRepository
     */
    protected $folderRepository;

    /**
     * @var MessageRepository
     */
    protected $messageRepository;


    /**
     * @var \Conjoon\User\User
     */
    protected $user;

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
            'messageRepository' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon\Data\Repository\Mail\MessageRepository'
            ),
            'user' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon\User\User'
            )
        ), $options);

        $this->messageRepository = $options['messageRepository'];
        $this->folderRepository  = $options['mailFolderRepository'];
        $this->user              = $options['user'];
    }

    /**
     * @inheritdoc
     */
    public function doesMailFolderExist(Folder $folder)
    {
        $path = $folder->getPath();

        if (!empty($path)) {
            $id = array_pop($path);
        } else {
            $id = $folder->getRootId();
        }

        try {

            $entity = $this->folderRepository->findById($id);

        } catch (\Conjoon\Argument\InvalidArgumentException $e) {

            throw new FolderServiceException(
                "Exception thrown by previous exception: " .
                $e->getMessage(),
                0, $e
            );
        }


        try {
            while ($entity && $entity->getParent()) {
                $entity = $entity->getParent();
            }
        } catch (\Exception $e) {
            // if an exception occurs, we assume the folder does
            // not exists
            return false;
        }

        return $entity !== null && ($folder->getRootId() == $entity->getId());
    }


    /**
     * @inheritdoc
     */
    public function isFolderAccountsRootFolder(Folder $folder) {
        $path = $folder->getPath();
        return $folder->getNodeId() === null &&
        empty($path) &&
        $this->getFolderType($folder->getRootId()) === self::ACCOUNTS_ROOT;
    }

    /**
     * @inheritdoc
     */
    public function isFolderRootFolder(Folder $folder) {
        $path = $folder->getPath();
        return $folder->getNodeId() === null &&
        empty($path) &&
        $this->getFolderType($folder->getRootId()) === self::ROOT;
    }

    /**
     * @inheritdoc
     */
    public function isFolderRepresentingRemoteMailbox(Folder $folder) {
        return $this->getFolderType($folder->getRootId()) === self::ROOT_REMOTE;
    }

    /**
     * Returns the type of the folder, i.e. the "type" property.
     *
     * @return string
     *
     * @throws FolderServiceException
     * @throws FolderDoesNotExistException
     */
    protected function getFolderType($folderId)
    {
        try {
            $entity = $this->folderRepository->findById($folderId);
        } catch (\Exception $e) {

            throw new FolderServiceException(
                "Exception thrown by previous exception: "
                . $e->getMessage(), 0, $e
            );
        }

        if ($entity === null) {

            throw new FolderDoesNotExistException(
                "Client Folder with id " . $folderId . " was not found"
            );
        }

        return $entity->getType();
    }

    /**
     * @inheritdoc
     */
    public function getChildFolderEntities($folder) {

        $data = array('folder' => $folder);

        $config = array(
            'folder' => array(
                array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Mail\Client\Folder\Folder'
                ),
                'OR',
                array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Data\Entity\Mail\MailFolderEntity'
                )
            )
        );

        ArgumentCheck::check($config, $data);

        $entity = $folder;

        if ($entity instanceof \Conjoon\Mail\Client\Folder\Folder) {
            $entity = $this->getFolderEntity($folder);
        }

        $folders = array();

        try {
            $folders = $this->folderRepository->getChildFolders($entity);
        } catch (\Exception $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

        return $folders;
    }

    /**
     * @inheritdoc
     */
    public function getFolderEntity(Folder $folder)
    {
        $id = $folder->getNodeId()
              ? $folder->getNodeId()
              : $folder->getRootId();

        $rootId = $folder->getRootId();


        try {
            $orgEntity = $this->folderRepository->findById($id);
        } catch (InvalidArgumentException $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

        if (!$orgEntity) {
            throw new FolderDoesNotExistException(
                "Folder $folder was not found"
            );
        }

        // we might have found a folder.
        // but we have to make sure that the node id we are inspecting is actually
        // an id in the data storage, and not simply the name of a folder (e.g. "2")
        $entity = $orgEntity;
        while (true) {
            if ($entity && $entity->getParent()) {
                $entity = $entity->getParent();
            } else {
                break;
            }
        }

        if ($entity->getId() != $rootId) {
            throw new FolderDoesNotExistException(
                "Folder $folder was not found"
            );
        }

        return $orgEntity;
    }

    /**
     * @inheritdoc
     */
    public function doesFolderAllowChildFolders(Folder $folder) {

        $entity = $this->getFolderEntity($folder);

        if (!$entity->getIsChildAllowed()) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function moveMessages($sourceFolder, $targetFolder) {

        $data = array(
            'sourceFolder' => $sourceFolder,
            'targetFolder' => $targetFolder,
        );

        $config = array(
            'sourceFolder' => array(
                array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Mail\Client\Folder\Folder'
                ),
                'OR',
                array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Data\Entity\Mail\MailFolderEntity'
                )
            ),
            'targetFolder' => array(
                array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Mail\Client\Folder\Folder'
                ),
                'OR',
                array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Data\Entity\Mail\MailFolderEntity'
                )
            )
        );

        ArgumentCheck::check($config, $data);

        $sourceEntity = null;
        $targetEntity = null;

        if (!($sourceFolder instanceof \Conjoon\Data\Entity\Mail\MailFolderEntity)) {
            $sourceEntity = $this->getFolderEntity($sourceFolder);
        } else {
            $sourceEntity = $sourceFolder;
        }
        if (!($targetFolder instanceof \Conjoon\Data\Entity\Mail\MailFolderEntity)) {
            $targetEntity = $this->getFolderEntity($targetFolder);
        } else {
            $targetEntity = $targetFolder;
        }

        try {
            $this->messageRepository->moveMessagesFromFolder($sourceEntity, $targetEntity);
        } catch (\Exception $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function hasMessages($folder) {
        $data = array(
            'folder' => $folder
        );

        $config = array(
            'folder' => array(
                array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Mail\Client\Folder\Folder'
                ),
                'OR',
                array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Data\Entity\Mail\MailFolderEntity'
                )
            )
        );

        ArgumentCheck::check($config, $data);

        $folderEntity = null;

        if (!($folder instanceof \Conjoon\Data\Entity\Mail\MailFolderEntity)) {
            $folderEntity = $this->getFolderEntity($folder);
        } else {
            $folderEntity = $folder;
        }

        try {
            return $this->folderRepository->hasMessages($folderEntity);
        } catch (\Exception $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function applyMailAccountsToFolder(Array $accounts, $folder) {

        $data = array(
            'accounts' => $accounts,
            'folder'   => $folder
        );

        $config = array(
            'accounts' => array(
                'type' => 'arrayType',
                'class' => '\Conjoon\Data\Entity\Mail\MailAccountEntity'
            ),
            'folder' => array(
                array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Mail\Client\Folder\Folder'
                ),
                'OR',
                array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Data\Entity\Mail\MailFolderEntity'
                )
            )
        );

        ArgumentCheck::check($config, $data);

        $folderEntity = null;

        if (!($folder instanceof \Conjoon\Data\Entity\Mail\MailFolderEntity)) {
            $folderEntity = $this->getFolderEntity($folder);
        } else {
            $folderEntity = $folder;
        }

        // inspect accounts
        $orgMailAccounts = $folderEntity->getMailAccounts();
        $oldIds = array();
        foreach ($orgMailAccounts as $orgAccount) {
            $oldIds[] = $orgAccount->getId();
        }

        foreach ($accounts as $account) {
            if (!in_array($account->getId(), $oldIds)) {

                $folderEntity->addMailAccount($account);

                // prevent dups
                $oldIds[] = $account->getId();
            }
        }

        try {
            $this->folderRepository->register($folderEntity);
            $this->folderRepository->flush();
        } catch (\Exception $e){
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

        return $folderEntity;
    }

}
