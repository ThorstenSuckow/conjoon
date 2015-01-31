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
     * @param DefaultGetMessageCacheKeyGen $keyGen
     */
    public function __construct(
        GetMessageCache $messageCache, DefaultGetMessageCacheKeyGen $keyGen) {

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

    /**
     * Tries to remove all cached items which can be identified given the
     * passed arguments. Missing identifiers for the cache key will be guessed
     * and added.
     *
     * @param int $messageId
     * @param int $userId
     * @param array $path
     *
     * @throws CacheServiceException
     */
    public function removeCachedItemsFor($messageId, $userId, $path) {

        $data = array(
            array('format' => 'plain', 'externalResources' => true),
            array('format' => 'plain', 'externalResources' => false),
            array('format' => 'html', 'externalResources' => true),
            array('format' => 'html', 'externalResources' => false),
        );

        $path = json_encode($path);

        foreach ($data as $keyConfig) {
            $this->remove(array(
                'messageId' => $messageId,
                'userId' => $userId,
                'path' => $path,
                'format' => $keyConfig['format'],
                'externalResources' => $keyConfig['externalResources']
            ));
        }
    }

}

