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
     * @return array an associative array with the following keys:
     * - combinedFile: a file name that should point to a file which holds
     * all the requested cache files already combined
     * - fileList: an array with file names where all the entries for the
     * requested caches can be found
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

        $combinedKeys = array();

        foreach ($caches as $key => $value) {
            if ($value) {
                $combinedKeys[] = $key;
            }
        }

        sort($combinedKeys, SORT_STRING);

        $combinedFile = $folder . '/' . implode('.', $combinedKeys) . '.list';

        foreach ($caches as $key => $value) {
            if ($value) {
                $fileList[] = $folder . '/' . $key . '.list';
            }
        }

        return array(
            'combinedFile' => $combinedFile,
            'fileList'     => $fileList

        );
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
     * @throws InvalidArgumentException, RuntimeException
     */
    public function getCacheEntryCountForUserId($userId, $folder)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId: $userId"
            );
        }

        $files = $this->getManifestFileListForUserId($userId, $folder);

        $combinedFile = $files['combinedFile'];
        $fileList     = $files['fileList'];

        $count = -1;

        if (@file_exists($combinedFile)) {
            $res    = file_get_contents($combinedFile);
            $res    = trim($res);
            $count  = $res ? count(explode("\n", $res)) : 0;
        } else {
            throw new RuntimeException(
                "Could not get cache count. Check if \"$combinedFile\" exists"
            );
        }

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