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

namespace Conjoon\Mail\Client\Service;

/**
 * @see MessageServiceFacade
 */
require_once 'Conjoon/Mail/Client/Service/MessageServiceFacade.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServiceResult\GetMessageServiceResult
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/GetMessageServiceResult.php';

use \Conjoon\Mail\Client\Service\ServiceResult\GetMessageServiceResult;


/**
 * Service facade for decorating a DefaultMessageServiceFacade with operations related to
 * caching.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class CacheableMessageServiceFacade implements MessageServiceFacade {

    /**
     * @type \Conjoon\Mail\Client\Service\DefaultMessageServiceFacade
     */
    protected $messageServiceFacade;

    /**
     * @type \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheService
     */
    protected $messageCacheService;

    /**
     * Creates a new instance of the MessageServiceFacade.
     *
     * @param DefaultMessageServiceFacade The service facade to decorate
     * @param \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheService $messageCacheService
     *        the cache service used for caching messages
     */
    public function __construct(
        DefaultMessageServiceFacade $messageServiceFacade,
        \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheService $messageCacheService) {

        $this->messageServiceFacade = $messageServiceFacade;
        $this->messageCacheService = $messageCacheService;
    }

    /**
     * @ineritdoc
     */
    public function setFlagsForMessagesInFolder($flag, $path, \Conjoon\User\User $user)
    {
        return $this->messageServiceFacade->setFlagsForMessagesInFolder(
            $flag, $path, $user
        );
    }

    /**
     * @inheritdoc
     */
    public function getUnformattedMessage($id, $path, \Conjoon\User\User $user)
    {
        return $this->messageServiceFacade->getUnformattedMessage(
            $id, $path, $user
        );
    }

    /**
     * @inheritdoc
     */
    public function getMessage(
        $id, $path, \Conjoon\User\User $user,
        \Conjoon\Mail\Client\Message\Strategy\ReadableStrategy $readableStrategy)
    {
        try {
            $messageCacheService = $this->messageCacheService;

            $format = 'plain';
            $externalResources = false;

            if ($readableStrategy instanceof
                \Conjoon\Mail\Client\Message\Strategy\HtmlReadableStrategy) {
                $format = 'html';
                $externalResources = $readableStrategy->areExternalResourcesAllowed();
            }

            $keyConfig = array(
                'userId' => $user->getId(),
                'messageId' => $id,
                'path' => $path,
                'format' => $format,
                'externalResources' => $externalResources
            );

            $data = $messageCacheService->load($keyConfig);

            if ($data === null) {
                $data = $this->messageServiceFacade->getMessage(
                    $id, $path, $user, $readableStrategy
                );

                if ($data->isSuccess()) {
                    $messageCacheService->save($data, $keyConfig);
                }

            }

            return $data;

        } catch (\Exception $e) {

            return new GetMessageServiceResult(new MessageServiceException(
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
        return $this->messageServiceFacade->getAttachment(
            $key, $uId, $path, $user
        );
    }

    /**
     * @inheritdoc
     */
    public function getMessageForReply(
        $id, $path, \Conjoon\User\User $user, $replyAll = false)
    {
        return $this->messageServiceFacade->getMessageForReply(
            $id, $path, $user, $replyAll
        );
    }

    /**
     * @inheritdoc
     */
    public function getMessageForForwarding($id, $path, \Conjoon\User\User $user)
    {
        return $this->messageServiceFacade->getMessageForForwarding(
            $id, $path, $user
        );
    }

    /**
     * @inheritdoc
     */
    public function getMessageForComposing($id, $path, \Conjoon\User\User $user)
    {
        return $this->messageServiceFacade->getMessageForComposing(
            $id, $path, $user
        );
    }

}
