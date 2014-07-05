<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
    Conjoon\Mail\Client\Folder\FolderServiceException;

/**
 * @see Conjoon\Mail\Client\Folder\FolderService
 */
require_once 'Conjoon/Mail/Client/Folder/FolderService.php';

/**
 * @see Conjoon\Mail\Client\Folder\FolderServiceException
 */
require_once 'Conjoon/Mail/Client/Folder/FolderServiceException.php';

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
     * @const ROOT_REMOTE
     */
    const ROOT_REMOTE = 'root_remote';

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
            )
        ), $options);

        $this->folderRepository  = $options['mailFolderRepository'];
        $this->user              = $options['user'];
        $this->mailFolderCommons = $options['mailFolderCommons'];
    }

    /**
     * @inheritdoc
     */
    public function isFolderRepresentingRemoteMailbox(Folder $folder)
    {
        try {
            $entity = $this->folderRepository->findById(
                $folder->getRootId()
            );
        } catch (\Exception $e) {

            throw new FolderServiceException(
                "Exception thrown by previous exception: "
                 . $e->getMessage(), 0, $e
            );
        }

        if ($entity === null) {

            throw new FolderServiceException(
                "Client folder with id " . $folder->getRootId() . " was not found"
            );
        }

        return $entity->getType() === self::ROOT_REMOTE;
    }

    /**
     * @inheritdoc
     */
    public function getFolderEntity(Folder $folder)
    {
        $id       = $folder->getNodeId()
                    ? $folder->getNodeId()
                    : $folder->getRootId();
        $rootId   = $folder->getRootId();

        try {
            $isRemote = $this->isFolderRepresentingRemoteMailbox($folder);
        } catch (FolderServiceException $e) {
            return null;
        }

        $entity = $this->folderRepository->findById($id);

        if (!$entity || !$isRemote) {
            return $entity;
        }

        $orgEntity = $entity;

        // if the folder is remote, we have to check if the root id
        // of the fodler matches the id of the root folder of the
        // found $entity
        while (true) {
            if (!$entity->getParent()) {
                if ($entity->getId() == $rootId) {
                    return $orgEntity;
                }
                break;
            }
            $entity = $entity->getParent();
        }

        return null;
    }


}