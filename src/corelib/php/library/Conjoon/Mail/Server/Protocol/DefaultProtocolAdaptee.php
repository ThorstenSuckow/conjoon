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


namespace Conjoon\Mail\Server\Protocol;


/**
 * @see \Conjoon\Mail\Server\Protocol\ProtocolAdaptee
 */
require_once 'Conjoon/Mail/Server/Protocol/ProtocolAdaptee.php';

/**
 * @see \Conjoon\Mail\Server\Protocol\DefaultResult\SetFlagsResult
 */
require_once 'Conjoon/Mail/Server/Protocol/DefaultResult/SetFlagsResult.php';


/**
 * A default implementation for a ProtocolAdaptee
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultProtocolAdaptee implements ProtocolAdaptee {

    /**
     * @var \Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository
     */
    protected $doctrineMailFolderRepository;

    /**
     * @var \Conjoon\Data\Repository\Mail\DoctrineMessageFlagRepository
     */
    protected $doctrineMessageFlagRepository;

    /**
     * @var array
     */
    protected $defaultClassNames = array(
        'folderSecurityService'
            => '\Conjoon\Mail\Client\Security\DefaultFolderSecurityService',
        'folderService'
        => '\Conjoon\Mail\Client\Folder\DefaultFolderService',
        'mailFolderCommons'
        => '\Conjoon\Mail\Client\Folder\DefaultFolderCommons',
        'imapMessageFlagRepository'
        => '\Conjoon\Data\Repository\Mail\ImapMessageFlagRepository'

    );

    /**
     * @var array
     */
    protected $cachedObjects = array(
        'folderSecurityService' => array(),
        'folderService'         => array(),
        'mailFolderCommons'     => array()
    );

    /**
     * Creates a new instance of this protocol adaptee.
     *
     * @param \Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository $doctrineMailFolderRepository
     * @param \Conjoon\Data\Repository\Mail\DoctrineMessageFlagRepository $doctrineMessageFlagRepository
     *
     *
     */
    public function __construct(
        \Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository $doctrineMailFolderRepository,
        \Conjoon\Data\Repository\Mail\DoctrineMessageFlagRepository $doctrineMessageFlagRepository)
    {
        $this->doctrineMailFolderRepository  = $doctrineMailFolderRepository;
        $this->doctrineMessageFlagRepository = $doctrineMessageFlagRepository;
    }


    /**
     * @inheritdoc
     */
    public function setFlags(
        \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $flagCollection,
        \Conjoon\User\User $user)
    {
        $folder = $flagCollection->getFolder();

        try {
            $mayWrite = $this->mayUserWriteFolder($folder, $user);
        } catch (\Conjoon\Mail\Client\Security\SecurityServiceException $e) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
            );
        }

        if (!$mayWrite) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "User must not access the folder \"" . $folder->getNodeId() ."\""
            );
        }

        try {
            $isRemoteMailbox = $this->isFolderRepresentingRemoteMailbox($folder, $user);
        } catch (\Conjoon\Mail\Client\Folder\FolderServiceException $e) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
            );
        }


        if ($isRemoteMailbox) {
            $rootFolder = $this->doctrineMailFolderRepository->findById(
                $folder->getRootId());

            $accounts = $rootFolder->getMailAccounts();

            if (count($accounts) > 1) {
                throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                    "Unexpected multiple accounts found for a remote folder"
                );
            }
            $imapMessageFlagRepository =
                $this->defaultClassNames['imapMessageFlagRepository'];
            $imapRepository = new $imapMessageFlagRepository(
                $accounts[0]
            );

            try {
                $imapRepository->setFlagsForUser($flagCollection, $user);
            } catch (\Exception $e) {
                throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                    "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
                );
            }
        } else {
            try {
                $this->applyFlagCollectionForUser($flagCollection, $user);
            } catch (\Conjoon\Data\Repository\Mail\MailRepositoryException $e) {
                throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                    "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
                );
            }
        }

        return new \Conjoon\Mail\Server\Protocol\DefaultResult\SetFlagsResult();
    }



// -------- helper API

    /**
     * Returns the DoctrineMailFolderRepository this class was configured with.
     *
     * @return \Conjoon\Data\Mail\DoctrineMailFolderRepository
     */
    protected function getDoctrineMailFolderRepository()
    {
        return $this->doctrineMailFolderRepository;
    }

    /**
     * Returns the DoctrineMessageFlagRepository this class was configured with.
     *
     * @return \Conjoon\Data\Mail\DoctrineMessageFlagRepository
     */
    protected function getDoctrineMessageFlagRepository()
    {
        return $this->doctrineMessageFlagRepository;
    }

    /**
     * Returns the folder security service for the specified user.
     *
     * @param \Conjoon\User\User $user
     *
     * @return \Conjoon\Mail\Client\Security\FolderSecurityService
     */
    protected function getFolderSecurityServiceForUser(\Conjoon\User\User $user)
    {
        return $this->getServiceForUser('folderSecurityService', $user);
    }

    /**
     * Returns the service for the specified user.
     *
     * @param \Conjoon\User\User $user
     *
     * @return mixed
     */
    protected function getServiceForUser($serviceName, \Conjoon\User\User $user)
    {
        $id = spl_object_hash($user);

        if (empty($this->cachedObjects[$serviceName])) {

            $className = $this->defaultClassNames[$serviceName];

            $instance = null;

            switch ($serviceName) {

                case 'mailFolderCommons':

                    $instance = new $className(array(
                        'user'                 => $user,
                        'mailFolderRepository' => $this->getDoctrineMailFolderRepository()
                    ));

                    break;

                case 'folderSecurityService':

                    $instance = new $className(array(
                        'mailFolderRepository' => $this->getDoctrineMailFolderRepository(),
                        'user'                 => $user,
                        'mailFolderCommons'    => $this->getServiceForUser(
                                                      'mailFolderCommons', $user
                                                  )
                    ));
                    break;

                case 'folderService':

                    $instance = new $className(array(
                        'mailFolderRepository' => $this->getDoctrineMailFolderRepository(),
                        'user'                 => $user,
                        'mailFolderCommons'    => $this->getServiceForUser(
                                                      'mailFolderCommons', $user
                                                  )
                    ));
                    break;
            }

            $this->cachedObjects[$serviceName][$id] = $instance;
        }

        return $this->cachedObjects[$serviceName][$id];
    }

    /**
     * Returns true if the folder represents a remote mailbox, otherwise false.
     *
     * @param \Conjoon\Mail\Client\Folder\Folder $folder
     *
     * @return boolean
     *
     * @throws \Conjoon\Mail\Client\Folder\FolderServiceException
     */
    protected function isFolderRepresentingRemoteMailbox(
        \Conjoon\Mail\Client\Folder\Folder $folder, \Conjoon\User\User $user)
    {
        return $this->getServiceForUser('folderService', $user)
                    ->isFolderRepresentingRemoteMailbox($folder);

    }

    /**
     *
     * @param \Conjoon\Mail\Client\Folder\Folder $folder
     * @param \Conjoon\User\User $user
     *
     *
     * @throws \Conjoon\Mail\Client\Security\SecurityServiceException
     */
    protected function mayUserWriteFolder(
        \Conjoon\Mail\Client\Folder\Folder $folder, \Conjoon\User\User $user)
    {
        $folderSecurityService = $this->getFolderSecurityServiceForUser($user);

        return $folderSecurityService->isFolderAccessible($folder);
    }

    /**
     * Applies the message flags for the specified user.
     *
     * @param \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $flagCollection
     * @param \Conjoon\User\User $user
     *
     * @throws \Conjoon\Data\Repository\Mail\MailRepositoryException
     */
    protected function applyFlagCollectionForUser(
        \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $folderFlagCollection,
        \Conjoon\User\User $user)
    {
        $this->getDoctrineMessageFlagRepository()->setFlagsForUser(
            $folderFlagCollection, $user
        );
    }

}