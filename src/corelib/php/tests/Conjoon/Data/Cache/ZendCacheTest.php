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

namespace Conjoon\Data\Cache;

/**
 * @see Conjoon\Data\Cache\ZendCache
 */
require_once 'Conjoon/Data/Cache/ZendCache.php';

/**
 * @see \Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ZendCacheTest extends \PHPUnit_Framework_TestCase {

    protected $zendCacheCore = null;

    protected $cache;

    protected $data = array();

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
            'key1' => array(1, 2, 3),
            'key2' => array('foo' => 'bar')
        );

        $this->cache = new \Conjoon\Data\Cache\ZendCache($this->zendCacheCore);
    }

    /**
     * Ensures everythingworks as expected.
     */
    public function testOk() {

        $cache = $this->cache;

        foreach ($this->data as $key => $data) {

            $this->assertNull($cache->load($key));

            $cache->save($data, $key);

            $this->assertSame($cache->load($key), $data);

            $this->assertTrue($cache->remove($key));
            $this->assertNull($cache->load($key));

        }

    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testLoadWithWrongArgumentType() {
        $this->cache->load(rand(1, 10000));
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
    public function testSaveWithWrongArgumentType() {
        $this->cache->save(array(), rand(1, 10000));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSaveWithTags() {

        $this->cache->save(array(), "2", array(1, 2, 3));

    }



}
