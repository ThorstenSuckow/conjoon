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
 * @see \Conjoon\Mail\Client\Service\ServiceResult\Cache\CacheServiceException
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/CacheServiceException.php';


use  \Conjoon\Mail\Client\Service\ServiceResult\GetMessageServiceResult;

/**
 * A service for managing GetMessageServiceResult caches.
 *
 * @package Conjoon
 * @category Conjoon\Service
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class GetMessageCacheService  {

    /**
     * @type GetMessageCache
     */
    protected $messageCache;

    /**
     * @type GetMessageCacheKeyGen
     */
    protected $keyGen;

    /**
     * @param GetMessageCache $messageCache
     * @param GetMessageCacheKeyGen $keyGen
     */
    public function __construct(
        GetMessageCache $messageCache, GetMessageCacheKeyGen $keyGen) {

        $this->messageCache = $messageCache;
        $this->keyGen = $keyGen;
    }

    /**
     * Returns the cache key generated for the specified data.
     *
     * @param array $keyConf The information to use for assembling the cache key
     *
     * @return \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey
     *
     * @throws CacheServiceException
     */
    public function getCacheKey(array $keyConf) {

        try {
            return $this->keyGen->generateKey($keyConf);
        }catch (\Exception $e) {
            throw new CacheServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }
    }

    /**
     * Tries to load a GetMessageServiceResult as identified by the key configuration.
     *
     * @param array $keyConf The information to use for assembling the cache key
     *
     * @return mixed the null|GetMessageServiceResult if there was a successfull cache hit,
     *               otherwise false
     *
     * @throws CacheServiceException
     */
    public function load(array $keyConf) {

        $cacheKey = $this->getCacheKey($keyConf);

        try {
            return $this->messageCache->load($cacheKey);
        }catch (\Exception $e) {
            throw new CacheServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

    }

    /**
     * Tries to save a GetMessageServiceResult using the key configuration as
     * the identifier.
     *
     * @param GetMessageServiceResult $serviceResult The getmessageServiceResult which
     *        should be saved
     * @param array $keyConf The information to use for assembling the cache key
     *
     * @return boolean
     *
     * @throws CacheServiceException
     */
    public function save(GetMessageServiceResult $serviceResult, array $keyConf) {

        $cacheKey = $this->getCacheKey($keyConf);

        try {
            return $this->messageCache->save($serviceResult, $cacheKey);
        }catch (\Exception $e) {
            throw new CacheServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

    }

    /**
     * Tries to remove a GetMessageServiceResult identified by the key configuration
     * from the cache.
     *
     * @param array $keyConf The information to use for assembling the cache key
     *
     * @return boolean
     *
     * @throws CacheServiceException
     */
    public function remove(array $keyConf) {

        $cacheKey = $this->getCacheKey($keyConf);

        try {
            return $this->messageCache->remove($cacheKey);
        }catch (\Exception $e) {
            throw new CacheServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

    }

}

