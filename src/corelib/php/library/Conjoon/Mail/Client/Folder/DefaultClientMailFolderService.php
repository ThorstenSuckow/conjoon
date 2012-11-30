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
    Conjoon\User\User,
    Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException;

/**
 * @see Conjoon\Mail\Client\Folder\ClientMailFolderService
 */
require_once 'Conjoon/Mail/Client/Folder/ClientMailFolderService.php';

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
                'class' => 'Conjoon\Mail\Client\Folder\MailFolderCommons'
            )
        ), $options);

        $this->folderRepository  = $options['mailFolderRepository'];
        $this->user              = $options['user'];
        $this->mailFolderCommons = $options['mailFolderCommons'];
    }

    /**
     * @inheritdoc
     */
    public function isClientMailFolderRepresentingRemoteMailbox(MailFolder $folder)
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