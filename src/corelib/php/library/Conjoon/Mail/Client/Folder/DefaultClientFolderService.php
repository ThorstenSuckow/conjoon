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

use Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository;

/**
 * @see Conjoon\Mail\Client\Folder\ClientFolderService
 */
require_once 'Conjoon/Mail/Client/Folder/ClientFolderService.php';

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
class DefaultClientFolderService implements ClientFolderService {

    /**
     * @const ROOT_REMOTE
     */
    const ROOT_REMOTE = 'root_remote';

    /**
     * @var DoctrineMailFolderRepository
     */
    protected $folderRepository;

    /**
     * Creates a new instance of this service.
     *
     * @param DoctrineMailFolderRepository $folderRepository the mail folder repository for
     *                                                       querying the underlying datastorage
     *
     */
    public function __construct(DoctrineMailFolderRepository $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }


    /**
     * @inheritdoc
     */
    public function isClientMailboxFolderRepresentingRemoteMailbox(
        \Conjoon_Mail_Client_Folder_ClientMailboxFolder $folder)
    {
        try {
            $entity = $this->folderRepository->findById(
                $folder->getRootId()
            );
        } catch (\Exception $e) {

            /**
             * @see Conjoon\Mail\Client\Folder\ClientFolderServiceException
             */
            require_once 'Conjoon/Mail/Client/Folder/ClientFolderServiceException.php';

            throw new \Conjoon\Mail\Client\Folder\ClientFolderServiceException(
                "Exception thrown by previous exception: "
                 . $e->getMessage(), 0, $e
            );
        }

        if ($entity === null) {
            /**
             * @see Conjoon\Mail\Client\Folder\ClientFolderServiceException
             */
            require_once 'Conjoon/Mail/Client/Folder/ClientFolderServiceException.php';

            throw new \Conjoon\Mail\Client\Folder\ClientFolderServiceException(
                "Client folder with id " . $folder->getRootId() . " was not found"
            );
        }

        return $entity->getType() === self::ROOT_REMOTE;

    }


}