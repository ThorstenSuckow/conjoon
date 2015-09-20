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

namespace Conjoon\Mail\Client\Security;

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException;

/**
 * @see MailFolderSecurityService
 */
require_once 'Conjoon/Mail/Client/Security/FolderSecurityService.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * @see Conjoon\Mail\Client\Security\SecurityServiceException
 */
require_once 'Conjoon/Mail/Client/Security/SecurityServiceException.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers
 */
require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersUsers.php';

/**
 * @see \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
 */
 require_once 'Conjoon/Mail/Client/Folder/FolderDoesNotExistException.php';

/**
 * @see \Conjoon\Mail\Client\Folder\NoChildFoldersAllowedException
 */
require_once 'Conjoon/Mail/Client/Folder/NoChildFoldersAllowedException.php';

/**
 * @see \Conjoon\Mail\Client\Folder\Folder
 */
require_once 'Conjoon/Mail/Client/Folder/Folder.php';

/**
 * @see \Conjoon\Mail\Client\Folder\DefaultFolderPath
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultFolderPath.php';


/**
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFolderSecurityService implements FolderSecurityService {

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
    protected $folderCommons;


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
        ), $options);

        $this->folderRepository = $options['mailFolderRepository'];
        $this->user             = $options['user'];
        $this->folderCommons    = $options['mailFolderCommons'];
    }

    /**
     * @inheritdoc
     */
    public function isFolderMovable(
        \Conjoon\Mail\Client\Folder\Folder $folder){
        return $this->isFolderAccessible($folder);
    }

    /**
     * @inheritdoc
     */
    public function mayAppendFolderTo(
        \Conjoon\Mail\Client\Folder\Folder $folder) {

        if (!$this->folderCommons->doesFolderAllowChildFolders($folder)) {
            throw new \Conjoon\Mail\Client\Folder\NoChildFoldersAllowedException(
                "Folder " . $folder . " does not allow child folders"
            );
        }

        return $this->isFolderAccessible($folder);
    }

    /**
     * @inheritdoc
     */
    public function isFolderAccessible(\Conjoon\Mail\Client\Folder\Folder $folder) {
        return $this->isFolderAccessibleHelper($folder, false);

    }

    /**
     * @inheritdoc
     */
    public function isFolderHierarchyAccessible(\Conjoon\Mail\Client\Folder\Folder $folder) {
        return $this->isFolderAccessibleHelper($folder, true);

    }

    /**
     * Helper function for folder*Accessible checks.
     * @param \Conjoon\Mail\Client\Folder\Folder $folder
     * @param bool $considerChildFolders True to recurse into child folders
     *                                   and check whether these are accessible
     *                                   too
     *
     * @return bool true if folder(s) are accessible, otherwise false
     *
     * @throws SecurityServiceException
     * @throws \Conjoon\Mail\Client\Folder\FolderDoesNotExistException
     */
    protected function isFolderAccessibleHelper(
        \Conjoon\Mail\Client\Folder\Folder $folder, $considerChildFolders = false)
    {
        $path   = $folder->getPath();
        $nodeId = $folder->getNodeId();
        $rootId = $folder->getRootId();

        $checkNodeId = null;

        $checkForRoot = false;

        // the folder that gets transformed to its corresponding entity later on
        $transformToEntity = $folder;

        switch (true) {

            // only root id available, check only root
            case (empty($path) && empty($nodeId)):

                $checkNodeId = $rootId;
                $checkForRoot = true;
                break;

            // paths set, node id available.
            case (!empty($path) && !empty($nodeId)):

                $doesMailFolderExist = $this->checkClientMailFolderExists($folder);

                // check if node id exists client side
                if ($doesMailFolderExist) {
                    // check if node is accessible
                    $checkNodeId = $nodeId;

                } else {
                    // check if root node is accessible
                    // if remote, check for rootID
                    if (!$this->folderCommons->isFolderRepresentingRemoteMailbox(
                        $folder)) {
                        throw new \Conjoon\Mail\Client\Folder\FolderDoesNotExistException(
                            "The folder $folder does not seem to exist"
                        );
                    }


                    $checkNodeId = $rootId;
                    $checkForRoot = true;
                }

                break;

            default:
                throw new SecurityServiceException(
                    "Could not check whether folder \""
                        . $folder->__toString()
                        . "\" is accessible "
                );

        }

        if ($checkForRoot) {
            // assemble new folder to check for availability of checkNodeId
            $folderCheck = new \Conjoon\Mail\Client\Folder\Folder(
                new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                    '["root", "' . $checkNodeId . '"]'
                )
            );

            $doesMailFolderExist = $this->checkClientMailFolderExists($folderCheck);

            $transformToEntity = $folderCheck;

            if (!$doesMailFolderExist) {
                throw new \Conjoon\Mail\Client\Folder\FolderDoesNotExistException(
                    "The folder $folder does not seem to exist"
                );
            }
        }

        return $this->checkFolderHierarchyAccessible(
            $this->folderCommons->getFolderEntity($transformToEntity),
            $considerChildFolders === true
        );

    }

    /**
     * Returns false if the $folderEntity or any of its child folders is
     * not accessible by the user bound to this service.
     *
     * @param \Conjoon\Data\Entity\Mail\MailFolderEntity
     * @param bool $recurse
     * @return bool
     */
    protected function checkFolderHierarchyAccessible(
        \Conjoon\Data\Entity\Mail\MailFolderEntity $folderEntity, $recurse = false) {
        /**
         * @refactor uses old implementation
         */

        $OWNER_STR = \Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers::OWNER;

        $foldersUsers =
            new \Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers();

        $checkNodeId = $folderEntity->getId();

        $rel = $foldersUsers->getRelationShipForFolderAndUser(
            $checkNodeId, $this->user->getId()
        );

        $isAccessible = $rel === $OWNER_STR;


        if ($isAccessible && $recurse) {
            $childFolders = $this->folderCommons->getChildFolderEntities($folderEntity);

            foreach ($childFolders as $childFolder) {
                $isAccessible = $this->checkFolderHierarchyAccessible($childFolder, $recurse);
                if (!$isAccessible) {
                    break;
                }
            }
        }

        return $isAccessible;
    }


    /**
     * Checks whether the specified folder exists locally.
     *
     * @param \Conjoon\Mail\Client\Folder\Folder $folder
     *
     * @throws SecurityServiceException
     */
    protected function checkClientMailFolderExists(\Conjoon\Mail\Client\Folder\Folder $folder) {

        try {
            $doesMailFolderExist =
                $this->folderCommons->doesMailFolderExist($folder);
        } catch (\Conjoon\Mail\Client\Folder\FolderServiceException $e) {
            throw new SecurityServiceException(
                "Exception thrown by previous exception: "
                . $e->getMessage, 0, $e
            );
        }

        return $doesMailFolderExist;
    }
}