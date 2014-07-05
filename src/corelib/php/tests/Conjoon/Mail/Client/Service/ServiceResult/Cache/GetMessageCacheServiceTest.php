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


namespace Conjoon\Mail\Client\Service\ServiceResult\Cache;

/**
 * @see DefaultGetMessageCache
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCacheService.php';

/**
 * @see DefaultGetMessageCacheKeyGen
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/DefaultGetMessageCacheKeyGen.php';

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
            new DefaultGetMessageCacheKeyGen
        );

        $this->data = array(
            array(array(
                'messageId' => 1,
                'userId' => 1,
                'path' => "[1, 2]",
                'format' => 'html',
                'externalResources' => true
            ), new GetMessageServiceResult("key1")),
            array(array(
                'messageId' => 1,
                'userId' => 1,
                'path' => "[1, 2]",
                'format' => 'plain',
                'externalResources' => true
            ), new GetMessageServiceResult("key2"))
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

        $key = $this->service->getCacheKey(array(
            'messageId' => 2,
            'userId' => 1,
            'path' => "[3, 4]",
            'format' => 'html',
            'externalResources' => true
        ));

        $this->assertTrue($key instanceof GetMessageCacheKey);

    }

    /**
     * Ensures everything works as expected
     */
    public function testRemoveCachedItemsFor() {

        $cache = $this->service;

        foreach ($this->data as $key => $data) {
            $key = $data[0];
            $data = $data[1];
            $this->assertNull($cache->load($key));
            $cache->save($data, $key);
        }

        foreach ($this->data as $key => $data) {
            $key = $data[0];
            $data = $data[1];

            $loaded = $cache->load($key);
            $this->assertTrue($loaded instanceof GetMessageServiceResult);
        }

        $cache->removeCachedItemsFor(1, 1, array(1, 2));

        foreach ($this->data as $key => $data) {
            $key = $data[0];
            $data = $data[1];
            $this->assertNull($cache->load($key));
        }

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
