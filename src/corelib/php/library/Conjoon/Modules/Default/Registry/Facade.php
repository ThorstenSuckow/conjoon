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
 * This facade eases the access to often needed operations on the registry.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Default_Registry_Facade {

    /**
     * @var Conjoon_Modules_Default_Registry_Facade
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Default_Registry_Model_Registry
     */
    private $_registryModel = null;

    /**
     * @var Conjoon_Modules_Default_Registry_Model_RegistryValues
     */
    private $_registryValuesModel = null;

    /**
     * @var Conjoon_Modules_Default_Registry_Filter_Registry
     */
    private $_updateEntriesFilter = null;

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
     * @return Conjoon_Modules_Default_Registry_Model_Registry
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
     * Sets the specified keys in $data to theri mapped values.
     *
     * @param array $data A numeric array with associative entries, i.e.
     *    [[ 'key' => '/somekey/anotherkey', 'value' => 1]]
     * @param integer $userId The id of the user for which the entries should
     * be set. If those entries do not already exist for the specified
     * userId, this entries will be created.
     *
     * @return array Returns an associative array with the following keys:
     * - updated: A list of keys that were actually updated
     * - failed: list with keys that could not be updated
     *
     * @throws InvalidArgumentException throws an InvalidArgumentException if
     * the specified $userId was noit valid
     * or Conjoon_Filter_Exception if the submitted data could not be sanitized
     */
    public function setEntriesFromDataForUserId(Array $data, $userId)
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - was \"$userId\""
            );
        }

        $filter = $this->_getUpdateEntriesFilter();

        $sanitized = array();

        try {
            for ($i = 0, $len = count($data); $i < $len; $i++) {
                $filter->setData($data[$i]);
                $sanitized[] = $filter->getProcessedData();
            }
        } catch (Zend_Filter_Exception $e) {
            /**
            * @see Conjoon_Error
            */
            require_once 'Conjoon/Error.php';

            $error = Conjoon_Error::fromFilter($filter, $e);

            /**
            * @see Conjoon_Filter_Exception
            */
            require_once 'Conjoon/Filter/Exception.php';

            throw new Conjoon_Filter_Exception($error->getMessage());
        }

        $registry = $this->getRegistryForUserId($userId, false);

        $updated = array();
        $failed  = array();

        for ($i = 0, $len = count($sanitized); $i < $len; $i++) {
            $failed[$sanitized[$i]['key']] = true;
        }

        for ($i = 0, $len = count($sanitized); $i < $len; $i++) {
            $key   = $sanitized[$i]['key'];
            $value = $sanitized[$i]['value'];

            $keys      = explode('/', $key);
            $valueName = array_pop($keys);
            $keys      = implode('/', $keys);
            $path      = $this->_pathToIndex($keys, $registry);

            if (!empty($path)) {
                $parent      = $registry[$path[count($path)-1]];
                $valueConfig = $parent['values'];
                $registryId  = $parent['id'];

                for ($a = 0, $lena = count($valueConfig); $a < $lena; $a++) {
                    if ($valueConfig[$a]['is_editable']
                        && $valueConfig[$a]['name'] == $valueName) {

                        $type = $valueConfig[$a]['type'];

                        switch ($type) {
                            case 'STRING':
                                $value = (string)$value;
                            break;

                            case 'BOOLEAN':
                                $value = (int)(bool)$value;
                            break;

                            case 'INTEGER':
                                $value = (int)$value;
                            break;

                            case 'FLOAT':
                                $value = (float)$value;
                            break;
                        }

                        $succ = $this->_getRegistryValuesModel()->updateValueForUser(
                            $registryId, $valueName, $value, $type, $userId
                        );

                        if ($succ) {
                            $updated[] = $key;
                            unset($failed[$key]);
                        }
                    }
                }
            }
        }

        return array(
            'updated' => $updated,
            'failed'  => array_keys($failed)
        );
    }


    /**
     * Returns the value for the specified key and the user.
     *
     * @param string $key
     * @param integer $userId
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getValueForKeyAndUserId($key, $userId)
    {
        $key = trim($key, '/');

        $userId = (int)$userId;

        if ($userId < 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - was \"$userId\""
            );
        }

        $keys     = explode('/', $key);
        $valueKey = array_pop($keys);
        $key      = implode('/', $keys);

        $entries = $this->_getRegistryModel()->getRegistryForUser($userId);
        $this->_mapApplicationConfiguration($entries);

        $path = $this->_pathToIndex($key, $entries);

        if (empty($path)) {
            return null;
        }

        $values = $entries[$path[count($path)-1]]['values'];

        for ($i = 0, $len = count($values); $i < $len; $i++) {
            if ($values[$i]['name'] == $valueKey) {
                switch ($values[$i]['type']) {
                    case 'STRING':
                        return (string)$values[$i]['value'];
                    case 'BOOLEAN':
                        return (bool)$values[$i]['value'];
                    case 'INTEGER':
                        return (int)$values[$i]['value'];
                    case 'FLOAT':
                        return (float)$values[$i]['value'];

                }
            }
        }

        return null;
    }

    /**
     * Returns the registry as an array of Conjoon_Modules_Default_Registry_Dto.
     *
     * @param integer $userId
     *
     * @return array|Conjoon_Modules_Groupware_Email_Folder_Dto an array of
     * Conjoon_Modules_Groupware_Email_Folder_Dto
     *
     * @throws InvalidArgumentException
     */
    public function getRegistryForUserId($userId, $toDto = true)
    {
        $userId = (int)$userId;

        if ($userId < 0) {
            throw new InvalidArgumentException(
                "Invalid argument for userId, got \"$userId\""
            );
        }

        $entries = $this->_getRegistryModel()->getRegistryForUser($userId);

        $this->_mapApplicationConfiguration($entries);

        return $toDto ? $this->_toDtos($entries) : $entries;
    }

// -------- api

    /**
     * Maps application specific registry values to the registry. Those
     * values are generally marked as not editable since they depend on
     * system configurations.
     * The values that get applied are:
     *
     * client/environment/device - either iphone or default (HTTP_USER_AGENT)
     * base/conjoon/name - hardcoded to "conjoon" as of now
     * base/conjoon/version - read from Conjoon_Version (Conjoon_Version::VERSION)
     * base/conjoon/edition - read from application config
     * server/php/max_execution_time - read from php ini
     * server/environment host, port, protocol - server environment settings
     *
     * @param array
     */
    private function _mapApplicationConfiguration(&$entries)
    {
        /**
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';


        // base/conjoon
        $baseName = $this->_pathToIndex('base/conjoon', $entries);
        if (!empty($baseName)) {
            $ind = array_pop($baseName);
            $entries[$ind]['values'][] = array(
                'name'        => 'name',
                'value'       => 'conjoon',
                'type'        => 'STRING',
                'is_editable' => 0
            );

            /**
             * @see Conjoon_Version
             */
            require_once 'Conjoon/Version.php';

            $entries[$ind]['values'][] = array(
                'name'        => 'version',
                'value'       => Conjoon_Version::VERSION,
                'type'        => 'STRING',
                'is_editable' => 0
            );

            $entries[$ind]['values'][] = array(
                'name'        => 'edition',
                'value'       => Zend_Registry::get(
                                     Conjoon_Keys::REGISTRY_CONFIG_OBJECT
                                 )->environment->edition,
                'type'        => 'STRING',
                'is_editable' => 0
            );
        }

        // client/environment
        $path = $this->_pathToIndex('client/environment', $entries);
        if (!empty($path)) {
            $entries[array_pop($path)]['values'][] = array(
                'name'        => 'device',
                'value'       => (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'ipod')
                                  || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'iphone'))
                                  ? 'iphone'
                                  : 'default',
                'type'        => 'STRING',
                'is_editable' => 0
            );
        }

        // server environment
        $path2 = $this->_pathToIndex('server/environment', $entries);
        if (!empty($path2)) {
            $ind = array_pop($path2);
            $entries[$ind]['values'][] = array(
                'name'        => 'host',
                'value'       => $_SERVER['SERVER_NAME'],
                'type'        => 'STRING',
                'is_editable' => 0
            );
            $entries[$ind]['values'][] = array(
                'name'        => 'port',
                'value'       => isset($_SERVER['SERVER_PORT'])
                                 ? $_SERVER['SERVER_PORT']
                                 : 80,
                'type'        => 'INTEGER',
                'is_editable' => 0
            );
            $entries[$ind]['values'][] = array(
                'name'        => 'protocol',
                'value'       =>  ((isset($_SERVER['HTTPS'])
                                  && (strtolower($_SERVER['HTTPS']) == "on"
                                     || $_SERVER['HTTPS'] == 1))
                                  || (isset($_SERVER['SERVER_PORT'])
                                      && $_SERVER['SERVER_PORT'] == 443)
                                  ? 'https'
                                  : 'http'),
                'type'        => 'STRING',
                'is_editable' => 0
            );
        }

        // server/php
        $path = $this->_pathToIndex('server/php', $entries);
        if (!empty($path)) {
            $entries[array_pop($path)]['values'][] = array(
                'name'        => 'max_execution_time',
                'value'       => ini_get('max_execution_time'),
                'type'        => 'INTEGER',
                'is_editable' => 0
            );
        }

    }

    /**
     * Finds the indexes of the path in entries and returns the array with
     * the found index, whereas the first index maps the first part of the
     * path and so on. Returns an empty array if the path could not be found.
     *
     * @param string $path
     * @param array $entries
     *
     * @return array
     */
    private function _pathToIndex($path, &$entries)
    {
        $path = trim($path, '/');

        $parts = explode('/', $path);

        $key = array_shift($parts);

        $parentId = 0;

        $found = array();

        for ($i = 0, $len = count($entries); $i < $len; $i++) {
            $entry =& $entries[$i];

            if ($entry['key'] == $key && $entry['parent_id'] == $parentId) {
                $found[] = $i;
                $parentId = $entry['id'];
                $key = array_shift($parts);
                if ($key === null) {
                    break;
                }
                $i = -1;
            }
        }

        if (count($found) != count(explode('/', $path))) {
            return array();
        }

        return $found;
    }

    /**
     * Transforms the specified array to a list of
     * Conjoon_Modules_Default_Registry_Dto
     *
     * @param array $entries
     * @return array
     */
    private function _toDtos(Array $entries)
    {
        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';

        /**
         * @see Conjoon_Modules_Default_Registry_Dto
         */
        require_once 'Conjoon/Modules/Default/Registry/Dto.php';

        $dtos = array();


        foreach($entries as $key => $value) {
            $dto = new Conjoon_Modules_Default_Registry_Dto();

            $dto->id       = $value['id'];
            $dto->parentId = $value['parent_id'];
            $dto->key      = $value['key'];

            $myValues = array();
            for ($i = 0, $len = count($value['values']); $i < $len; $i++) {
                $mv =& $value['values'][$i];
                Conjoon_Util_Array::camelizeKeys($mv);
                $myValues[] = $mv;
            }

            $dto->values = $myValues;

            $dtos[] = $dto;
        }

        return $dtos;

    }

    /**
     *
     * @return Conjoon_Modules_Default_Registry_Model_Registry
     */
    private function _getRegistryModel()
    {
        if (!$this->_registryModel) {
             /**
             * @see Conjoon_Modules_Default_Registry_Model_Registry
             */
            require_once 'Conjoon/Modules/Default/Registry/Model/Registry.php';

            $this->_registryModel = new Conjoon_Modules_Default_Registry_Model_Registry();
        }

        return $this->_registryModel;
    }

    /**
     *
     * @return Conjoon_Modules_Default_Registry_Model_RegistryValues
     */
    private function _getRegistryValuesModel()
    {
        if (!$this->_registryValuesModel) {
             /**
             * @see Conjoon_Modules_Default_Registry_Model_RegistryValues
             */
            require_once 'Conjoon/Modules/Default/Registry/Model/RegistryValues.php';

            $this->_registryValuesModel = new Conjoon_Modules_Default_Registry_Model_RegistryValues();
        }

        return $this->_registryValuesModel;
    }

    /**
     *
     * @see Conjoon_Modules_Default_Registry_Filter_Registry
     */
    private function _getUpdateEntriesFilter()
    {
        if (!$this->_updateEntriesFilter) {

            /**
             * @see Conjoon_Modules_Default_Registry_Filter_Registry
             */
            require_once 'Conjoon/Modules/Default/Registry/Filter/Registry.php';

            $this->_updateEntriesFilter = new Conjoon_Modules_Default_Registry_Filter_Registry(
                array(),
                Conjoon_Filter_Input::CONTEXT_UPDATE
            );
        }

        $this->_updateEntriesFilter->setData(array());
        return $this->_updateEntriesFilter;

    }


}