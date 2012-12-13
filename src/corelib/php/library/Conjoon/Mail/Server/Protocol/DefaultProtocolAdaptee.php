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
     * @var \Conjoon\Data\Repository\Mail\DoctrineMailAccountRepository
     */
    protected $doctrineMailAccountRepository;


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
        => '\Conjoon\Data\Repository\Mail\ImapMessageFlagRepository',
        'imapMessageRepository'
        => '\Conjoon\Data\Repository\Mail\ImapMessageRepository',
        'imapAttachmentRepository'
        => '\Conjoon\Data\Repository\Mail\ImapAttachmentRepository',
        'accountService'
        => '\Conjoon\Mail\Client\Account\DefaultAccountService'

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
     * @param \Conjoon\Data\Repository\Mail\DoctrineMailAccountRepository $doctrineMailAccountRepository

     *
     */
    public function __construct(
        \Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository $doctrineMailFolderRepository,
        \Conjoon\Data\Repository\Mail\DoctrineMessageFlagRepository $doctrineMessageFlagRepository,
        \Conjoon\Data\Repository\Mail\DoctrineMailAccountRepository $doctrineMailAccountRepository)
    {
        $this->doctrineMailFolderRepository  = $doctrineMailFolderRepository;
        $this->doctrineMessageFlagRepository = $doctrineMessageFlagRepository;
        $this->doctrineMailAccountRepository = $doctrineMailAccountRepository;

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

            try{
                $account = $this->getAccountServiceForUser($user)
                                ->getMailAccountToAccessRemoteFolder($folder);

                if ($account) {
                    $imapMessageFlagRepository =
                        $this->defaultClassNames['imapMessageFlagRepository'];
                    $imapRepository = new $imapMessageFlagRepository($account);
                    $imapRepository->setFlagsForUser($flagCollection, $user);
                }
            } catch (\Exception $e) {
                throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                    "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
                );
            }
            if (!$account) {
                throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                    "No mail account found for folder"
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

        /**
         * @see \Conjoon\Mail\Server\Protocol\DefaultResult\SetFlagsResult
         */
        require_once 'Conjoon/Mail/Server/Protocol/DefaultResult/SetFlagsResult.php';

        return new \Conjoon\Mail\Server\Protocol\DefaultResult\SetFlagsResult();
    }

    /**
     * @inheritdoc
     */
    public function getMessage(
            \Conjoon\Mail\Client\Message\MessageLocation $messageLocation,
            \Conjoon\User\User $user)
    {
        $folder = $messageLocation->getFolder();

        try {
            $mayRead = $this->mayUserReadFolder($folder, $user);
        } catch (\Conjoon\Mail\Client\Security\SecurityServiceException $e) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
            );
        }

        if (!$mayRead) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "User must not access the folder \"" . $folder->getNodeId() ."\""
            );
        }

        try {
            $isRemoteMailbox = $this->isFolderRepresentingRemoteMailbox(
                $folder, $user);
        } catch (\Conjoon\Mail\Client\Folder\FolderServiceException $e) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

        if (!$isRemoteMailbox) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "No support for folders representing POP3 mailboxes"
            );
        }

        try{
            $account = $this->getAccountServiceForUser($user)
                ->getMailAccountToAccessRemoteFolder($folder);

            if ($account) {
                $imapMessageRepository =
                    $this->defaultClassNames['imapMessageRepository'];
                $imapRepository = new $imapMessageRepository($account);

                $entity = $imapRepository->findById($messageLocation);

                if ($entity == null) {
                    throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                        "Message not found"
                    );
                }

            }
        } catch (\Exception $e) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
            );
        }
        if (!$account) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "No mail account found for folder"
            );
        }
        /**
         * @see \Conjoon\Mail\Server\Protocol\DefaultResult\GetMessageResult
         */
        require_once 'Conjoon/Mail/Server/Protocol/DefaultResult/GetMessageResult.php';

        return new \Conjoon\Mail\Server\Protocol\DefaultResult\GetMessageResult(
            $entity,$messageLocation
        );

    }

    /**
     * @inheritdoc
     */
    public function getAttachment(
        \Conjoon\Mail\Client\Message\AttachmentLocation $attachmentLocation,
        \Conjoon\User\User $user)
    {

        $messageLocation = $attachmentLocation->getMessageLocation();

        $folder = $messageLocation->getFolder();

        try {
            $mayRead = $this->mayUserReadFolder($folder, $user);
        } catch (\Conjoon\Mail\Client\Security\SecurityServiceException $e) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
            );
        }

        if (!$mayRead) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "User must not access the folder \"" . $folder->getNodeId() ."\""
            );
        }

        try {
            $isRemoteMailbox = $this->isFolderRepresentingRemoteMailbox(
                $folder, $user);
        } catch (\Conjoon\Mail\Client\Folder\FolderServiceException $e) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

        if (!$isRemoteMailbox) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "No support for folders representing POP3 mailboxes"
            );
        }

        try{
            $account = $this->getAccountServiceForUser($user)
                ->getMailAccountToAccessRemoteFolder($folder);

            if ($account) {
                $imapAttachmentRepository =
                    $this->defaultClassNames['imapAttachmentRepository'];
                $imapRepository = new $imapAttachmentRepository($account);

                $entity = $imapRepository->findById($attachmentLocation);

                if ($entity == null) {
                    throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                        "Message not found"
                    );
                }

            }
        } catch (\Exception $e) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
            );
        }
        if (!$account) {
            throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
                "No mail account found for folder"
            );
        }
        /**
         * @see \Conjoon\Mail\Server\Protocol\DefaultResult\GetMessageResult
         */
        require_once 'Conjoon/Mail/Server/Protocol/DefaultResult/GetAttachmentResult.php';

        return new \Conjoon\Mail\Server\Protocol\DefaultResult\GetAttachmentResult(
            $entity, $attachmentLocation
        );

    }



// -------- helper API

    /**
     * Returns the DoctrineMailAccountRepository this class was configured with.
     *
     * @return \Conjoon\Data\Mail\DoctrineMailAccountRepository
     */
    protected function getDoctrineMailAccountRepository()
    {
        return $this->doctrineMailAccountRepository;
    }

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
     * Returns the accountservice for the specified user.
     *
     * @param \Conjoon\User\User $user
     *
     * @return \Conjoon\Mail\Client\Account\AccountService
     */
    protected function getAccountServiceForUser(\Conjoon\User\User $user)
    {
        return $this->getServiceForUser('accountService', $user);
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

                case 'accountService':
                    $instance = new $className(array(
                        'user'                 => $user,
                        'folderService'        => $this->getServiceForUser(
                                                      'folderService', $user
                                                  ),
                        'mailAccountRepository' => $this->getDoctrineMailAccountRepository()
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
     *
     * @param \Conjoon\Mail\Client\Folder\Folder $folder
     * @param \Conjoon\User\User $user
     *
     *
     * @throws \Conjoon\Mail\Client\Security\SecurityServiceException
     */
    protected function mayUserReadFolder(
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