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
    Conjoon\Argument\InvalidArgumentException,
    Conjoon\Util\ArrayUtil;

/**
 * @see MailFolderCommons
 */
require_once 'Conjoon/Mail/Client/Folder/FolderCommons.php';

/**
 * @see FolderDoesNotExistException
 */
require_once 'Conjoon/Mail/Client/Folder/FolderDoesNotExistException.php';

/**
 * @see IllegalFolderRootTypeException
 */
require_once 'Conjoon/Mail/Client/Folder/IllegalFolderRootTypeException.php';

/**
 * @see FolderOperationProtocolSupportException
 */
require_once 'Conjoon/Mail/Client/Folder/FolderOperationProtocolSupportException.php';

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
 * @see Conjoon\Mail\Client\Folder\InvalidFolderTypeException
 */
require_once 'Conjoon/Mail/Client/Folder/InvalidFolderTypeException.php';

/**
 * @see Conjoon\Mail\Client\Folder\IllegalChildFolderTypeException
 */
require_once 'Conjoon/Mail/Client/Folder/IllegalChildFolderTypeException.php';

/**
 * @see Conjoon\Mail\Client\Folder\FolderTypes
 */
require_once 'Conjoon/Mail/Client/Folder/FolderTypes.php';

/**
 * @see Conjoon\Util\ArrayUtil
 */
require_once 'Conjoon/Util/ArrayUtil.php';


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
     * @const FOLDERTYPE_FOLDER
     */
    const FOLDERTYPE_FOLDER = 'folder';

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

        $config = $this->getFolderHybridCheckConfiguration('folder');

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
            )
        );

        ArrayUtil::apply(
            $config,
            $this->getFolderHybridCheckConfiguration('targetFolder'));
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

        $config = $this->getFolderHybridCheckConfiguration('folder');

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
    public function removeMailAccountsFromFolder($folder) {

        $data = array(
            'folder'   => $folder
        );

        $config = $this->getFolderHybridCheckConfiguration('folder');

        ArgumentCheck::check($config, $data);

        $folderEntity = null;

        if (!($folder instanceof \Conjoon\Data\Entity\Mail\MailFolderEntity)) {
            $folderEntity = $this->getFolderEntity($folder);
        } else {
            $folderEntity = $folder;
        }

        $this->removeAccounts($folderEntity);

        try {
            $this->folderRepository->flush();
        } catch (\Exception $e){
            throw new FolderServiceException(
                "Exception thrown by previous exception: " .
                $e->getMessage(), 0, $e
            );
        }

        return $folderEntity;
    }

    /**
     * Helper function for #removeMailAccountsFromFolder.
     *
     * @param \Conjoon\Data\Entity\Mail\MailFolderEntity $folderEntity
     */
    protected function removeAccounts(
        \Conjoon\Data\Entity\Mail\MailFolderEntity $folderEntity) {

        // inspect accounts
        $orgMailAccounts = $folderEntity->getMailAccounts();
        foreach ($orgMailAccounts as $orgAccount) {
            $folderEntity->removeMailAccount($orgAccount);
        }

        try {
            $this->folderRepository->register($folderEntity);
        } catch (\Exception $e){
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

        try {
            $folders = $this->folderRepository->getChildFolders($folderEntity);
        } catch (\Exception $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

        foreach ($folders as $folder) {
            $this->removeAccounts($folder);
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
                'type'       => 'arrayType',
                'minLength'  => 1,
                'allowEmpty' => false,
                'class'      => '\Conjoon\Data\Entity\Mail\MailAccountEntity'
            )
        );

        ArrayUtil::apply(
            $config,
            $this->getFolderHybridCheckConfiguration('folder'));
        ArgumentCheck::check($config, $data);

        $folderEntity = null;
        $rootId       = null;

        if (!($folder instanceof \Conjoon\Data\Entity\Mail\MailFolderEntity)) {
            $folderEntity = $this->getFolderEntity($folder);
            $rootId       = $folder->getRootId();
        } else {
            $folderEntity = $folder;

            try {
                $parent = $folderEntity;
                while ($parent->getParent()) {
                    $parent = $parent->getParent();
                }
                $rootId = $parent->getId();
            } catch (\Exception $e) {
                throw new FolderServiceException(
                    "Exception thrown by previous exception: " .
                    $e->getMessage(),
                    0, $e
                );
            }

        }

        // check if folder is remote folder
        $type = $this->getFolderType($rootId);

        if ($type === self::ROOT) {
            throw new IllegalFolderRootTypeException(
                "Folder's root folder does not support to be used by multiple mail accounts."
            );
        }
        if ($type === self::ROOT_REMOTE) {
            throw new FolderOperationProtocolSupportException(
                "Remote folder does not support to be used by multiple mail accounts."
            );
        }

        $this->applyAccounts($accounts, $folderEntity);

        try {
            $this->folderRepository->flush();
        } catch (\Exception $e){
            throw new FolderServiceException(
                "Exception thrown by previous exception: " .
                $e->getMessage(), 0, $e
            );
        }

        return $folderEntity;
    }

    /**
     * Helper function for #applyMailAccountsToFolder.
     *
     * @param Array $accounts
     * @param \Conjoon\Data\Entity\Mail\MailFolderEntity $folderEntity
     */
    protected function applyAccounts(Array $accounts,
        \Conjoon\Data\Entity\Mail\MailFolderEntity $folderEntity) {

        // inspect accounts
        $orgMailAccounts = $folderEntity->getMailAccounts();
        $oldIds = array();
        foreach ($orgMailAccounts as $orgAccount) {
            $oldIds[] = $orgAccount->getId();
        }

        foreach ($accounts as $applyAccount) {
            if (!in_array($applyAccount->getId(), $oldIds)) {
                $folderEntity->addMailAccount($applyAccount);
                // prevent dups
                $oldIds[] = $applyAccount->getId();
            }
        }

        try {
            $this->folderRepository->register($folderEntity);
        } catch (\Exception $e){
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

        try {
            $folders = $this->folderRepository->getChildFolders($folderEntity);
        } catch (\Exception $e) {
            throw new FolderServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

        foreach ($folders as $folder) {
            $this->applyAccounts($accounts, $folder);
        }
    }

    /**
     * @inheritdoc
     */
    public function applyTypeToFolder(
        $type, $folder, $childFolders = false) {

        $data = array(
            'type'               => $type,
            'folder'             => $folder,
            'childFolders'       => $childFolders
        );

        $config = array(
            'type' => array(
                'type'       => 'string',
                'allowEmpty' => false,
                'strict'     => true
            ),
            'childFolders' => array(
                'type'       => 'boolean',
                'allowEmpty' => false,
                'strict'     => true
            )
        );

        ArrayUtil::apply(
            $config,
            $this->getFolderHybridCheckConfiguration('folder'));

        ArgumentCheck::check($config, $data);

        $folderEntity = null;

        $type = $data['type'];

        // checks for supplied folder types
        if (!in_array($type, FolderTypes::getFolderTypes())) {
            throw new InvalidFolderTypeException(
                "invalid folder type \"$type\"."
            );
        }

        if ($childFolders === true &&
            in_array($type, FolderTypes::getFirstLevelFolderTypes())) {
            throw new IllegalChildFolderTypeException(
                "Folder type \"$type\" may not be applied to child folders."
            );
        }

        if (!($folder instanceof \Conjoon\Data\Entity\Mail\MailFolderEntity)) {
            $folderEntity = $this->getFolderEntity($folder);
        } else {
            $folderEntity = $folder;
        }

        $this->applyType($type, $folderEntity, $childFolders);

        try {
            $this->folderRepository->flush();
        } catch (\Exception $e){
            throw new FolderServiceException(
                "Exception thrown by previous exception: " .
                $e->getMessage(), 0, $e
            );
        }

        return $folderEntity;
    }

    /**
     * Helper function for applyTypeToFolder.
     *
     * @param string $type The type to apply
     * @param \Conjoon\Data\Entity\Mail\MailFolderEntity $folder The target folder
     * @param boolean $childFolders True to recurse into child folders
     *
     * @throws FolderServiceException
     * @throws FolderDoesNotExistException
     */
    protected function applyType(
        $type,
        \Conjoon\Data\Entity\Mail\MailFolderEntity $folder,
        $childFolders) {


        try {
            $this->folderRepository->register($folder);
        } catch (\Exception $e){
            throw new FolderServiceException(
                "Exception thrown by previous exception: " .
                $e->getMessage()
                , 0, $e
            );
        }

        $folder->setType($type);

        if ($childFolders) {
            $subs = $this->getChildFolderEntities($folder);
            foreach ($subs as $sub) {
                $this->applyType($type, $sub, true);
            }
        }
    }


    /**
     * Helper function to return the configuration for an argument check
     * for a Folder/MailFolderEntity argument.
     *
     * @param string $keyName The name used for the key which has to match the
     *        argument in the check
     *
     * @return array
     */
    protected function getFolderHybridCheckConfiguration($keyName) {
        return array($keyName => array(
            array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\Mail\Client\Folder\Folder'
            ),
            'OR',
            array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\Data\Entity\Mail\MailFolderEntity'
            )
        ));
    }

}
