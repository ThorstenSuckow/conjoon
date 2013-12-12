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
 * @see CacheableMessageServiceFacade
 */
require_once 'Conjoon/Mail/Client/Service/CacheableMessageServiceFacade.php';

/**
 * @see CacheableMessageServiceFacade
 */
require_once 'Conjoon/Mail/Client/Service/DefaultMessageServiceFacadeTest.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKeyGen
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCacheKeyGen.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCacheKey.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCache
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCache.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheService
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCacheService.php';

use \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKeyGen,
    \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey,
    \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCache,
    \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheService;


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class CacheableMessageServiceFacadeTest extends DefaultMessageServiceFacadeTest {


    protected function getMessageServiceFacade($server, $mailAccountRepository, $mailFolderRepository) {

        $messageCacheService = new GetMessageCacheService(
            new MockGetMessageCache,
            new MockGetMessageCacheKeyGen
        );

        return new CacheableMessageServiceFacade(
            new DefaultMessageServiceFacade(
            $server, $mailAccountRepository, $mailFolderRepository
            ),
            $messageCacheService
        );
    }


}

class MockGetMessageCacheKeyGen implements GetMessageCacheKeyGen {

    public function generateKey($data) {
        return new GetMessageCacheKey(json_encode(array($data)));
    }
}

class MockGetMessageCache implements GetMessageCache {

    protected $data = array();

    public function load($id) {

        $id = $id->getValue();

        return isset($this->data[$id])
            ? $this->data[$id]
            : null;
    }

    public function save($data, $id, array $tags = array()) {

        $id = $id->getValue();

        $this->data[$id] = $data;
        return true;
    }

    public function remove($id) {

        $id = $id->getValue();

        if (isset($this->data[$id])) {
            unset($this->data[$id]);
            return true;
        }


        return false;
    }



}
