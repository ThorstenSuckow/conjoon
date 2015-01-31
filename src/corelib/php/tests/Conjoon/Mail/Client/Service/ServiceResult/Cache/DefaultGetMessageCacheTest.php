<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/DefaultGetMessageCache.php';

/**
 * @see GetMessageCacheKey
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCacheKey.php';

/**
 * @see GetMessageServiceResult
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/GetMessageServiceResult.php';

/**
 * @see \Zend_Cache
 */
require_once 'Zend/Cache.php';

use \Conjoon\Mail\Client\Service\ServiceResult\GetMessageServiceResult;

/**
 * @category   Conjoon
 * @package    Conjoon_Service
 * @subpackage UnitTests
 * @group      Conjoon_Service
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultGetMessageCacheTest extends \PHPUnit_Framework_TestCase {

    protected $zendCacheCore = null;

    protected $cache;

    protected $data = array();

    protected $argServiceResult;

    protected $argCacheKey;

    public function setUp()
    {
        parent::setUp();

        $frontendOptions = array(
            'lifetime' => 7200, // cache lifetime of 2 hours
            'automatic_serialization' => true
        );

        $backendOptions = array(
            'cache_dir' => realpath(dirname(__FILE__)) .'/files' // Directory where to put the cache files
        );

        // getting a Zend_Cache_Core object
        $this->zendCacheCore = \Zend_Cache::factory('Core',
            'File',
            $frontendOptions,
            $backendOptions);


        $this->data = array(
            array(new GetMessageCacheKey('key1'), new GetMessageServiceResult("key1")),
            array(new GetMessageCacheKey('key2'), new GetMessageServiceResult("key2"))
        );

        $this->argServiceResult = new GetMessageServiceResult("argTest");
        $this->argCacheKey = new GetMessageCacheKey("argTest");

        $this->cache = new DefaultGetMessageCache($this->zendCacheCore);
    }

    /**
     * Ensures everythingworks as expected.
     */
    public function testOk() {

        $cache = $this->cache;

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
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testLoadWithWrongArgumentType() {
        $this->cache->load(" " . rand(1, 10000));
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testRemoveWithWrongArgumentType() {
        $this->cache->remove(rand(1, 10000));
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testSaveWithWrongArgumentTypeForId() {
        $this->cache->save($this->argServiceResult, rand(1, 10000));
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testSaveWithWrongArgumentTypeForData() {
        $this->cache->save("wh0t", $this->argCacheKey);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSaveWithTags() {

        $this->cache->save($this->argServiceResult, $this->argCacheKey, array(1, 2, 3));

    }




}
