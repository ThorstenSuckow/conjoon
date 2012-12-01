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
 * @see Conjoon_Argument_Check
 */
require_once 'Conjoon/Argument/Check.php';

/**
 * @see Conjoon_Mail_Client_Folder_ClientMailFolder
 */
require_once 'Conjoon/Mail/Client/Folder/Folder.php';

/**
 * @see Conjoon_Mail_Client_Folder_DefaultClientMailFolderPath
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultFolderPath.php';

/**
 * @see Conjoon_Mail_Client_Message_Flag_DefaultClientMessageFlagCollection
 */
require_once 'Conjoon/Mail/Client/Message/Flag/DefaultClientMessageFlagCollection.php';

/**
 * @see Conjoon_Mail_Client_Message_Flag_FolderMessageFlagCollection
 */
require_once 'Conjoon/Mail/Client/Message/Flag/FolderMessageFlagCollection.php';

/**
 * @see MessageServiceException
 */
require_once 'Conjoon/Mail/Client/Service/MessageServiceException.php';

/**
 * @see MessageServiceFacade
 */
require_once 'Conjoon/Mail/Client/Service/MessageServiceFacade.php';


/**
 * Service facade for operations related to messages. A default implementation
 * for Conjoon_Mail_Client_ClientMessageServiceFacade
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
     * @var \Conjoon\Mail\Client\Folder\ClientFolderService
     */
    protected $clientFolderService;

    /**
     * Creates a new instance of this service facade.
     *
     * @param \Conjoon\Mail\Client\Folder\ClientFolderService
     *
     *
     */
    public function __construct(
        \Conjoon\Mail\Client\Folder\FolderService $clientFolderService)
    {
        $this->clientFolderService = $clientFolderService;
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
     * @param \Conjoon_User_AppUser $user The user object representing the user
     *                                   who triggered this operation
     *
     * @throws Conjoon_Mail_Client_Service_ClientMessageServiceException
     */
    public function setFlagsForMessagesInFolder($flag, $path, \Conjoon\User\User $user)
    {
        $data = array(
            'flag' => $flag,
            'path' => $path
        );

        try {
            \Conjoon_Argument_Check::check(array(
                'flagString' => array(
                    'type'       => 'string',
                    'allowEmpty' => false
                ),
                'pathString' => array(
                    'type'       => 'string',
                    'allowEmpty' => false
                )
            ), $data);
        } catch (\Conjoon_Argument_Exception $e) {
            throw new \Conjoon_Mail_Client_Service_ClientMessageServiceException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

        $flagString = $data['flag'];
        $pathString = $data['path'];

        try {

            $folderPath =
                new \Conjoon_Mail_Client_Folder_DefaultClientMailboxFolderPath(
                    $pathString
                );

            $folder =
                new \Conjoon_Mail_Client_Folder_ClientMailboxFolder($folderPath);

            $flagCollection =
                new \Conjoon_Mail_Client_Message_Flag_DefaultClientMessageFlagCollection(
                    $flagString
                );

            $folderFlagCollection =
                new \Conjoon_Mail_Client_Message_Flag_FolderMessageFlagCollection(
                    $flagCollection, $folder
                );

            // check if the client folder submitted represents a remote folder
            if ($this->clientFolderService->isClientMailboxFolderRepresentingRemoteMailbox(
                $folder)) {
                //new RemoteService($DoctrineMessageRepository)->updateMessageFlagInFolder())
            } else {
                $this->clientMessageService->updateMessageFlagsInFolders();
            }


        } catch (\Conjoon_Argument_Exception $e) {
            throw new \Conjoon_Mail_Client_Service_ClientMessageServiceException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        } catch (\Conjoon_Mail_Client_MailClientException $e) {
            throw new \Conjoon_Mail_Client_Service_ClientMessageServiceException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        } catch (\Conjoon\Mail\Client\Folder\ClientFolderServiceException $e) {
            throw new \Conjoon_Mail_Client_Service_ClientMessageServiceException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

    }




}