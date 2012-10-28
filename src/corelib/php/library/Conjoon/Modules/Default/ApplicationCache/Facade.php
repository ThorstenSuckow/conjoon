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

/**
 * This facade eases the access to often needed operations regarding backend
 * implementations of the ApplicationCache.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Default_ApplicationCache_Facade {

    /**
     * @var Conjoon_Modules_Default_ApplicationCache_Facade
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Default_Registry_Facade
     */
    private $_registryFacade = null;

    /**
     * Enforce singleton.
     *
     */
    private function __construct()
    {
    }

    /**
     * Enforce singleton.
     *
     */
    private function __clone()
    {
    }

    /**
     *
     * @return Conjoon_Modules_Default_ApplicationCache_Facade
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


// -------- public api

    /**
     * Returns the list of files that hold the cache entries: based on the user
     * settings, only this files will be returned which represent a group
     * of filetypes that should get cached.
     *
     * @param integer $userId The user id for which the fileList should be
     * returned
     * @param string $folder The folder where the manifest files resist
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getManifestFileListForUserId($userId, $folder)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId: $userId"
            );
        }

        $facade = $this->_getRegistryFacade();

        $baseKey = '/client/applicationCache/';

        $caches = array(
            'images' => $facade->getValueForKeyAndUserId(
                $baseKey . 'cache-images', $userId
            ),
            'sounds' => $facade->getValueForKeyAndUserId(
                $baseKey . 'cache-sounds', $userId
            ),
            'flash' => $facade->getValueForKeyAndUserId(
                $baseKey . 'cache-flash', $userId
            ),
            'javascript' => $facade->getValueForKeyAndUserId(
                $baseKey . 'cache-javascript', $userId
            ),
            'html' => $facade->getValueForKeyAndUserId(
                $baseKey . 'cache-html', $userId
            ),
            'stylesheets' => $facade->getValueForKeyAndUserId(
                $baseKey . 'cache-stylesheets', $userId
            )
        );

        $fileList = array();

        foreach ($caches as $key => $value) {
            if ($value) {
                $fileList[] = $folder . '/' . $key . '.list';
            }
        }

        return $fileList;
    }

    /**
     * Returns the Unix timestamp in milliseconds of the cache last changes,
     * i.e. the last time the user altered the options regarding file-types
     * to cache.
     *
     * @param integer $userId The id of the user for whom the lastChanged-
     * timestamp should get returned.
     *
     * @return float
     *
     * @throws InvalidArgumentException
     */
    public function getCacheLastChangedTimestampForUserId($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId: $userId"
            );
        }

        $facade = $this->_getRegistryFacade();

        return $facade->getValueForKeyAndUserId(
            '/client/applicationCache/last-changed', $userId
        );
    }

    /**
     * Sets the last cached microtimestamp for the specified userId.
     *
     * @param float $microtime The time in milliseconds
     * @param integer $userId The id of the user for whom the lastChanged-
     * timestamp should be set.
     *
     *
     * @throws InvalidArgumentException
     */
    public function setCacheLastChangedTimestampForUserId($microtime, $userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId: $userId"
            );
        }

        $facade = $this->_getRegistryFacade();

        return $facade->setEntriesFromDataForUserId(
            array(array(
                'key'   => '/client/applicationCache/last-changed',
                'value' => $microtime
            )),
            $userId
        );
    }

    /**
     * Returns the number of cache entries that should get cached locally.
     * The number depends of the user settings, i.e. which kind of filetypes
     * he wishes to cache.
     *
     * @param integer $userId The id of the user for whom the number of cache
     * entries should be returned.
     * @param string $folder The folder where the manifest files resist
     *
     * @return integer
     *
     * @throws InvalidArgumentException
     */
    public function getCacheEntryCountForUserId($userId, $folder)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId: $userId"
            );
        }

        $fileList = $this->getManifestFileListForUserId($userId, $folder);

        $count = 0;
        for ($i = 0, $len = count($fileList); $i < $len; $i++) {
            $res    = file_get_contents($fileList[$i]);
            $res    = trim($res);
            $count += $res ? count(explode("\n", $res)) : 0;
        }

        // actually, we have to consider that the manifest file itself and
        // the page where the manifest file gets delivered do also count
        // as cache entries, so simply add them up here
        $count += 2;

        return $count;
    }

// -------- API

    /**
     * @return Conjoon_Modules_Default_Registry_Facade
     */
    private function _getRegistryFacade()
    {
        if (!$this->_registryFacade) {

            /**
             * @see Conjoon_Modules_Default_Registry_Facade
             */
            require_once 'Conjoon/Modules/Default/Registry/Facade.php';

            $this->_registryFacade = Conjoon_Modules_Default_Registry_Facade
                                     ::getInstance();
        }

        return $this->_registryFacade;
    }


}