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
 * @see Conjoon\Mail\Client\Folder\ClientMailFolderService
 */
require_once 'Conjoon/Mail/Client/Folder/ClientMailFolderService.php';

/**
 * @see Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DoctrineMailFolderRepository.php';


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
class DefaultClientMailFolderService implements ClientMailFolderService {

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
     * @inheritdoc
     */
    public function __construct(MailFolderRepository $folderRepository, User $user)
    {
        $this->folderRepository = $folderRepository;
    }

    /**
     * @inheritdoc
     */
    public function isClientMailFolderAccessible(ClientMailFolder $folder)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isClientMailFolderRepresentingRemoteMailbox(ClientMailFolder $folder)
    {
        try {
            $entity = $this->folderRepository->findById(
                $folder->getRootId()
            );
        } catch (\Exception $e) {

            /**
             * @see Conjoon\Mail\Client\Folder\ClientMailFolderServiceException
             */
            require_once 'Conjoon/Mail/Client/Folder/ClientMailFolderServiceException.php';

            throw new \Conjoon\Mail\Client\Folder\ClientMailFolderServiceException(
                "Exception thrown by previous exception: "
                 . $e->getMessage(), 0, $e
            );
        }

        if ($entity === null) {
            /**
             * @see Conjoon\Mail\Client\Folder\ClientMailFolderServiceException
             */
            require_once 'Conjoon/Mail/Client/Folder/ClientMailFolderServiceException.php';

            throw new \Conjoon\Mail\Client\Folder\ClientMailFolderServiceException(
                "Client folder with id " . $folder->getRootId() . " was not found"
            );
        }

        return $entity->getType() === self::ROOT_REMOTE;

    }


}