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
 * @see MessageServiceFacade
 */
require_once 'Conjoon/Mail/Client/Service/MessageServiceFacade.php';

/**
 * @see DefaultServiceResult
 */
require_once 'Conjoon/Mail/Client/Service/DefaultServiceResult.php';

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
     * Creates a new instance of the MessageServiceFacade.
     *
     * @param \Conjoon\Mail\Server\DefaultServer $server The mail server the
     *        service facade should be using
     *
     */
    public function __construct(\Conjoon\Mail\Server\DefaultServer $server)
    {
        $this->server = $server;
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




}