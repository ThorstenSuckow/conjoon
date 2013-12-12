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


namespace Conjoon\Mail\Client\Service\ServiceResult\Cache;

/**
 * @see DefaultGetMessageCache
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCacheService.php';

/**
 * @see GetMessageCacheKeyGen
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCacheKeyGen.php';

/**
 * @see GetMessageCacheKey
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCacheKey.php';


/**
 * @see GetMessageCache
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCache.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServiceResult\GetMessageServiceResult
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/GetMessageServiceResult.php';


use \Conjoon\Mail\Client\Service\ServiceResult\GetMessageServiceResult;

/**
 * @category   Conjoon
 * @package    Conjoon_Service
 * @subpackage UnitTests
 * @group      Conjoon_Service
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class GetMessageCacheServiceTest extends \PHPUnit_Framework_TestCase {

    protected $service;

    protected $data;

    public function setUp()
    {
        parent::setUp();

        $this->service = new GetMessageCacheService(
            new MockGetMessageCache,
            new MockGetMessageCacheKeyGen
        );

        $this->data = array(
            array(array('key1'), new GetMessageServiceResult("key1")),
            array(array('key2'), new GetMessageServiceResult("key2"))
        );
    }

    /**
     * Ensures everythingworks as expected.
     */
    public function testOk() {

        $cache = $this->service;

        foreach ($this->data as $key => $data) {

            $key = $data[0];
            $data = $data[1];

            $this->assertNull($cache->load($key));

            $cache->save($data, $key);

            $loaded = $cache->load($key);
            $this->assertTrue($loaded instanceof GetMessageServiceResult);

            $this->assertSame($loaded->toJson(), $data->toJson());

            $this->assertTrue($cache->remove($key));
            $this->assertNull($cache->load($key));

        }

    }

    /**
     * Ensures everything works as expected
     */
    public function testGetCacheKey() {

        $cache = $this->service;

        $key = $this->service->getCacheKey(array('data'));

        $this->assertTrue($key instanceof GetMessageCacheKey);

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
