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

namespace Conjoon\Data\Cache;

/**
 * @see \Conjoon\Data\Cache\Cacheable
 */
require_once 'Conjoon/Data/Cache/Cacheable.php';

/**
 * @see \Conjoon\Data\Cache\CacheException
 */
require_once 'Conjoon/Data/Cache/CacheException.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

use \Conjoon\Argument\ArgumentCheck;

/**
 * Simple wrapper for utilizing Zend_Cache_core as the cache core
 * implementation.
 *
 * @category   Conjoon_Cache
 * @package    Cache
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class ZendCache implements Cacheable {

    /**
     * @type \Zend_Cache_Core
     */
    protected $zendCacheCore;

    /**
     * Creates a new instance with Zend_Cache_Core as
     * the cache core implementation.
     *
     * @param \Zend_Cache_Core $zendCacheCore
     */
    public function __construct(\Zend_Cache_Core $zendCacheCore) {
        $this->zendCacheCore = $zendCacheCore;
    }


    /**
     * @inheritdoc
     *
     * @param string $id
     */
    public function load($id) {

        $args = array('id' => $id);

        ArgumentCheck::check(array(
            'id' => array(
                'type' => 'string',
                'allowEmpty' => false,
                'strict' => true
            )
        ), $args);

        $id = $args['id'];

        try {
            $data = $this->zendCacheCore->load($id);

            if ($data === false) {
                return null;
            }

            return $data;

        } catch (\Exception $e) {
            throw new CacheException(
                "exception thrown by previous exception", 0, $e
            );
        }


    }

    /**
     * @inheritdoc
     *
     * @param string $id
     */
    public function save($data, $id, array $tags = array()) {

        if (!empty($tags)) {
            throw new \RuntimeException("no support for tags yet");
        }

        $args = array('id' => $id);

        ArgumentCheck::check(array(
            'id' => array(
                'type' => 'string',
                'allowEmpty' => false,
                'strict' => true
            )
        ), $args);

        $id = $args['id'];

        try {
            return $this->zendCacheCore->save($data, $id, $tags);
        } catch (\Exception $e) {
            throw new CacheException(
                "exception thrown by previous exception", 0, $e
            );
        }


    }

    /**
     * @inheritdoc
     *
     * @param string $id
     */
    public function remove($id) {

        $args = array('id' => $id);

        ArgumentCheck::check(array(
            'id' => array(
                'type' => 'string',
                'allowEmpty' => false,
                'strict' => true
            )
        ), $args);

        $id = $args['id'];

        try {
            return $this->zendCacheCore->remove($id);
        } catch (\Exception $e) {
            throw new CacheException(
                "exception thrown by previous exception", 0, $e
            );
        }


    }

}
