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
     * @type \Zend_Cache_Frontend
     */
    protected $zendCacheFrontend;

    /**
     * Creates a new instance of the MessageServiceFacade.
     *
     * @param DefaultMessageServiceFacade The service facade to decorate
     * @param \Zend_Cache_Core $zendCacheFrontend the Zend Cache object used for
     * caching messages
     */
    public function __construct(
        DefaultMessageServiceFacade $messageServiceFacade, \Zend_Cache_Core $zendCacheFrontend) {

        $this->messageServiceFacade = $messageServiceFacade;
        $this->zendCacheFrontend = $zendCacheFrontend;
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
            $cache = $this->zendCacheFrontend;

            $folderPath = new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                $path
            );

            $format = 'plain';
            $externalResources = 0;

            if ($readableStrategy instanceof
                \Conjoon\Mail\Client\Message\Strategy\HtmlReadableStrategy) {
                $format = 'html';
                $externalResources = (int) $readableStrategy->areExternalResourcesAllowed();
            }

            $cacheId = $user->getId() .
                       '_' .
                       $id .
                       '_' .
                       $folderPath->getRootId() .
                       '_' .
                       implode('_', $folderPath->getPath()) .
                       '_' .
                       $format .
                       '_' .
                       $externalResources;

            if (!($cache->test($cacheId))) {
                $data = $this->messageServiceFacade->getMessage(
                    $id, $path, $user, $readableStrategy
                );

                if ($data->isSuccess()) {
                    $cache->save($data, $cacheId);
                }

            } else {
                $data = $cache->load($cacheId);
            }

            return $data;

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
