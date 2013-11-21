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

namespace Conjoon\Mail\Client\Service;

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see MessageServiceFacade
 */
require_once 'Conjoon/Mail/Client/Service/MessageServiceFacade.php';

/**
 * @see DefaultServiceResult
 */
require_once 'Conjoon/Mail/Client/Service/DefaultServiceResult.php';

use Conjoon\Argument\ArgumentCheck;

/**
 * Service facade for operations related to messages. A default implementation
 * for MessageServiceFacade
 * This service facade is adjusted to accept parameters prepared by a client
 * communicating over the http protocol.
 *
 * Note:
 * Default implementations of the Service Facades in the Conjoon_Mail_Client
 * rely heavily on parameter formats dictated by the client.
 *
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMessageServiceFacade implements MessageServiceFacade {

    /**
     * @protected \Conjoon\Mail\Server\DefaultServer
     */
    protected $server;

    /**
     * @type \Conjoon\Data\Repository\Mail\MailAccountRepository
     */
    protected $mailAccountRepository;

    /**
     * @type \Conjoon\Data\Repository\Mail\MailFolderRepository
     */
    protected $mailFolderRepository;

    /**
     * Creates a new instance of the MessageServiceFacade.
     *
     * @param \Conjoon\Mail\Server\DefaultServer $server The mail server the
     *        service facade should be using
     * @param \Conjoon\Data\Repository\Mail\MailAccountRepository $mailAccountRepository
     * @param \Conjoon\Data\Repository\Mail\MailFolderRepository $mailFolderRepository
     */
    public function __construct(
        \Conjoon\Mail\Server\DefaultServer $server,
        \Conjoon\Data\Repository\Mail\MailAccountRepository $mailAccountRepository,
        \Conjoon\Data\Repository\Mail\MailFolderRepository $mailFolderRepository
    )
    {
        $this->server = $server;

        $this->mailAccountRepository = $mailAccountRepository;
        $this->mailFolderRepository  = $mailFolderRepository;
    }

    /**
     * Updates the messages in the specified folder with the specified flag
     * settings.
     *
     * @param string $flag A jsonified array in the form of
     *                           '[{"id":"56","isRead":true}]'
     * @param string $path A path string in the form of
     *                           '["root","1","2"]', whereas the first index
     *                           would be the type of the root folder, the second
     *                           index the database id of the root folder,
     *                           and beginning with the third index the path
     *                           parts of the folder requested by the client.
     * @param \Conjoon\User\User $user The user object representing the user
     *                                   who triggered this operation
     *
     * @return ServiceResult
     */
    public function setFlagsForMessagesInFolder($flag, $path, \Conjoon\User\User $user)
    {
        try {

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderPath
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderPath.php';

            $folderPath = new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                $path
            );

            /**
             * @see \Conjoon\Mail\Client\Folder\Folder
             */
            require_once 'Conjoon/Mail/Client/Folder/Folder.php';

            $folder = new \Conjoon\Mail\Client\Folder\Folder($folderPath);

            /**
             * @see \Conjoon\Mail\Client\Message\Flag\DefaultFlagCollection
             */
            require_once 'Conjoon/Mail/Client/Message/Flag/DefaultFlagCollection.php';

            $flagCollection =
                new \Conjoon\Mail\Client\Message\Flag\DefaultFlagCollection(
                    $flag
                );

            /**
             * @see \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection
             */
            require_once 'Conjoon/Mail/Client/Message/Flag/FolderFlagCollection.php';

            $folderFlagCollection =
                new \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection(
                    $flagCollection, $folder
                );

            /**
             * @see \Conjoon\Mail\Server\Request\DefaultSetFlagsRequest
             */
            require_once 'Conjoon/Mail/Server/Request/DefaultSetFlagsRequest.php';

            $request = new \Conjoon\Mail\Server\Request\DefaultSetFlagsRequest(array(
                'user'       => $user,
                'parameters' => array(
                    'folderFlagCollection' => $folderFlagCollection
            )));

            $response = $this->server->handle($request);

            return new DefaultServiceResult($response);

        } catch (\Exception $e) {

            return new DefaultServiceResult(new MessageServiceException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            ));

        }

    }

    /**
     * @inheritdoc
     */
    public function getUnformattedMessage($id, $path, \Conjoon\User\User $user)
    {
        try {

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderPath
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderPath.php';

            $folderPath = new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                $path
            );

            /**
             * @see \Conjoon\Mail\Client\Folder\Folder
             */
            require_once 'Conjoon/Mail/Client/Folder/Folder.php';

            $folder = new \Conjoon\Mail\Client\Folder\Folder($folderPath);

            /**
             * @see \Conjoon\Mail\Client\Message\DefaultMessageLocation
             */
            require_once 'Conjoon/Mail/Client/Message/DefaultMessageLocation.php';

            $location = new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                $folder, $id
            );

            $request = new \Conjoon\Mail\Server\Request\DefaultGetMessageRequest(array(
                'user'       => $user,
                'parameters' => array(
                    'messageLocation' => $location
                )));

            $response = $this->server->handle($request);

            return new DefaultServiceResult(
                $response
            );

        } catch (\Exception $e) {

            return new DefaultServiceResult(new MessageServiceException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            ));

        }
    }

    /**
     * @inheritdoc
     */
    public function getMessage(
        $id, $path, \Conjoon\User\User $user,
        \Conjoon\Mail\Client\Message\Strategy\ReadableStrategy $readableStrategy)
    {
        try {

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderPath
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderPath.php';

            $folderPath = new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                $path
            );

            /**
             * @see \Conjoon\Mail\Client\Folder\Folder
             */
            require_once 'Conjoon/Mail/Client/Folder/Folder.php';

            $folder = new \Conjoon\Mail\Client\Folder\Folder($folderPath);

            /**
             * @see \Conjoon\Mail\Client\Message\DefaultMessageLocation
             */
            require_once 'Conjoon/Mail/Client/Message/DefaultMessageLocation.php';

            $location = new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                $folder, $id
            );

            $request = new \Conjoon\Mail\Server\Request\DefaultGetMessageRequest(array(
                'user'       => $user,
                'parameters' => array(
                    'messageLocation' => $location
                )));

            $response = $this->server->handle($request);

            /**
             * @see \Conjoon\Mail\Client\Service\ServicePatron\ReadMessagePatron
             */
            require_once 'Conjoon/Mail/Client/Service/ServicePatron/ReadMessagePatron.php';

            return new DefaultServiceResult(
                $response,
                new \Conjoon\Mail\Client\Service\ServicePatron\ReadMessagePatron(
                    $readableStrategy
                )
            );

        } catch (\Exception $e) {

            return new DefaultServiceResult(new MessageServiceException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            ));

        }
    }

    /**
     * @inheritdoc
     */
    public function getAttachment($key, $uId, $path, \Conjoon\User\User $user)
    {
        try {

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderPath
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderPath.php';

            $folderPath = new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                $path
            );

            /**
             * @see \Conjoon\Mail\Client\Folder\Folder
             */
            require_once 'Conjoon/Mail/Client/Folder/Folder.php';

            $folder = new \Conjoon\Mail\Client\Folder\Folder($folderPath);

            /**
             * @see \Conjoon\Mail\Client\Message\DefaultMessageLocation
             */
            require_once 'Conjoon/Mail/Client/Message/DefaultMessageLocation.php';

            $location = new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                $folder, $uId
            );

            /**
             * @see \Conjoon\Mail\Client\Message\DefaultAttachmentLocation
             */
            require_once 'Conjoon/Mail/Client/Message/DefaultAttachmentLocation.php';

            $attachmentLocation = new \Conjoon\Mail\Client\Message\DefaultAttachmentLocation(
                $location, $key
            );

            $request = new \Conjoon\Mail\Server\Request\DefaultGetAttachmentRequest(array(
                'user'       => $user,
                'parameters' => array(
                    'attachmentLocation' => $attachmentLocation
                )));

            $response = $this->server->handle($request);

            return new DefaultServiceResult(
                $response,
                new \Conjoon\Mail\Client\Service\ServicePatron\DownloadAttachmentPatron()
            );

        } catch (\Exception $e) {

            return new DefaultServiceResult(new MessageServiceException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            ));

        }
    }

    /**
     * @inheritdoc
     */
    public function getMessageForReply(
        $id, $path, \Conjoon\User\User $user, $replyAll = false)
    {
        try {

            $data = array('replyAll' => $replyAll);

            ArgumentCheck::check(array(
                'replyAll' => array(
                    'type'      => 'boolean',
                    'allowEmpty' => false
            )), $data);

            $replyAll = $data['replyAll'];

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderPath
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderPath.php';

            $folderPath = new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                $path
            );

            /**
             * @see \Conjoon\Mail\Client\Folder\Folder
             */
            require_once 'Conjoon/Mail/Client/Folder/Folder.php';

            $folder = new \Conjoon\Mail\Client\Folder\Folder($folderPath);

            /**
             * @see \Conjoon\Mail\Client\Message\DefaultMessageLocation
             */
            require_once 'Conjoon/Mail/Client/Message/DefaultMessageLocation.php';

            $location = new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                $folder, $id
            );

            $request = new \Conjoon\Mail\Server\Request\DefaultGetMessageRequest(array(
                'user'       => $user,
                'parameters' => array(
                    'messageLocation' => $location
                )));

            $response = $this->server->handle($request);

            /**
             * @see \Conjoon\Mail\Client\Service\ServicePatron\ReplyMessagePatron
             */
            require_once 'Conjoon/Mail/Client/Service/ServicePatron/ReplyMessagePatron.php';

            /**
             * @see \Conjoon\Mail\Client\Account\DefaultAccountService
             */
            require_once 'Conjoon/Mail/Client/Account/DefaultAccountService.php';

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderService
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderService.php';

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderCommons
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderCommons.php';

            $accountService = new \Conjoon\Mail\Client\Account\DefaultAccountService(
                array(
                    'user'                  => $user,
                    'mailAccountRepository' => $this->mailAccountRepository,
                    'folderService'         =>
                        new \Conjoon\Mail\Client\Folder\DefaultFolderService(array(
                            'user'                 => $user,
                            'mailFolderRepository' => $this->mailFolderRepository,
                            'mailFolderCommons'    =>
                                new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(array(
                                    'user' => $user,
                                    'mailFolderRepository' => $this->mailFolderRepository
            ))))));

            return new DefaultServiceResult(
                $response,
                new \Conjoon\Mail\Client\Service\ServicePatron\ReplyMessagePatron(
                    $accountService, $replyAll
                )
            );

        } catch (\Exception $e) {

            return new DefaultServiceResult(new MessageServiceException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            ));

        }
    }

    /**
     * @inheritdoc
     */
    public function getMessageForForwarding($id, $path, \Conjoon\User\User $user)
    {
        try {

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderPath
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderPath.php';

            $folderPath = new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                $path
            );

            /**
             * @see \Conjoon\Mail\Client\Folder\Folder
             */
            require_once 'Conjoon/Mail/Client/Folder/Folder.php';

            $folder = new \Conjoon\Mail\Client\Folder\Folder($folderPath);

            /**
             * @see \Conjoon\Mail\Client\Message\DefaultMessageLocation
             */
            require_once 'Conjoon/Mail/Client/Message/DefaultMessageLocation.php';

            $location = new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                $folder, $id
            );

            $request = new \Conjoon\Mail\Server\Request\DefaultGetMessageRequest(array(
                'user'       => $user,
                'parameters' => array(
                    'messageLocation' => $location
                )));

            $response = $this->server->handle($request);

            /**
             * @see \Conjoon\Mail\Client\Service\ServicePatron\ForwardMessagePatron
             */
            require_once 'Conjoon/Mail/Client/Service/ServicePatron/ForwardMessagePatron.php';

            /**
             * @see \Conjoon\Mail\Client\Account\DefaultAccountService
             */
            require_once 'Conjoon/Mail/Client/Account/DefaultAccountService.php';

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderService
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderService.php';

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderCommons
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderCommons.php';

            $accountService = new \Conjoon\Mail\Client\Account\DefaultAccountService(
                array(
                    'user'                  => $user,
                    'mailAccountRepository' => $this->mailAccountRepository,
                    'folderService'         =>
                    new \Conjoon\Mail\Client\Folder\DefaultFolderService(array(
                        'user'                 => $user,
                        'mailFolderRepository' => $this->mailFolderRepository,
                        'mailFolderCommons'    =>
                        new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(array(
                            'user' => $user,
                            'mailFolderRepository' => $this->mailFolderRepository
                        ))))));

            return new DefaultServiceResult(
                $response,
                new \Conjoon\Mail\Client\Service\ServicePatron\ForwardMessagePatron(
                    $accountService
                )
            );

        } catch (\Exception $e) {

            return new DefaultServiceResult(new MessageServiceException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            ));

        }
    }

    /**
     * @inheritdoc
     */
    public function getMessageForComposing($id, $path, \Conjoon\User\User $user)
    {
        try {

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderPath
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderPath.php';

            $folderPath = new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                $path
            );

            /**
             * @see \Conjoon\Mail\Client\Folder\Folder
             */
            require_once 'Conjoon/Mail/Client/Folder/Folder.php';

            $folder = new \Conjoon\Mail\Client\Folder\Folder($folderPath);

            /**
             * @see \Conjoon\Mail\Client\Message\DefaultMessageLocation
             */
            require_once 'Conjoon/Mail/Client/Message/DefaultMessageLocation.php';

            $location = new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                $folder, $id
            );

            $request = new \Conjoon\Mail\Server\Request\DefaultGetMessageRequest(array(
                'user'       => $user,
                'parameters' => array(
                    'messageLocation' => $location
                )));

            $response = $this->server->handle($request);

            /**
             * @see \Conjoon\Mail\Client\Service\ServicePatron\ReadMessagePatron
             */
            require_once 'Conjoon/Mail/Client/Service/ServicePatron/ReadMessagePatron.php';

            /**
             * @see \Conjoon\Mail\Client\Account\DefaultAccountService
             */
            require_once 'Conjoon/Mail/Client/Account/DefaultAccountService.php';

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderService
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderService.php';

            /**
             * @see \Conjoon\Mail\Client\Folder\DefaultFolderCommons
             */
            require_once 'Conjoon/Mail/Client/Folder/DefaultFolderCommons.php';

            $accountService = new \Conjoon\Mail\Client\Account\DefaultAccountService(
                array(
                    'user'                  => $user,
                    'mailAccountRepository' => $this->mailAccountRepository,
                    'folderService'         =>
                    new \Conjoon\Mail\Client\Folder\DefaultFolderService(array(
                        'user'                 => $user,
                        'mailFolderRepository' => $this->mailFolderRepository,
                        'mailFolderCommons'    =>
                        new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(array(
                            'user' => $user,
                            'mailFolderRepository' => $this->mailFolderRepository
                        ))))));

            return new DefaultServiceResult(
                $response,
                new \Conjoon\Mail\Client\Service\ServicePatron\EditMessagePatron(
                    $accountService
                )
            );

        } catch (\Exception $e) {

            return new DefaultServiceResult(new MessageServiceException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            ));

        }
    }

}
