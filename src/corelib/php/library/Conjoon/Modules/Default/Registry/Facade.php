<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * This facade eases the access to often needed operations on the registry.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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
     * Returns the registry as an array of Conjoon_Modules_Default_Registry_Dto.
     *
     * @param integer $userId
     *
     * @return array|Conjoon_Modules_Groupware_Email_Folder_Dto an array of
     * Conjoon_Modules_Groupware_Email_Folder_Dto
     *
     * @throws InvalidArgumentException
     */
    public function getRegistryForUserId($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument for userId, got \"$userId\""
            );
        }

        $entries = $this->_getRegistryModel()->getRegistryForUser($userId);

        $this->_mapApplicationConfiguration($entries);

        return $this->_toDtos($entries);
    }

// -------- api

    /**
     * Maps application specific registry values to the registry. Those
     * values are generally marked as not editable since they depend on
     * system configurations.
     * The values that get applied are:
     *
     * service/youtube/chromeless/api-key - read form application config
     * client/environment/device - either iphone or default (HTTP_USER_AGENT)
     * base/conjoon/name - hardcoded to "conjoon" as of now
     * base/conjoon/version - read from Conjoon_Version (Conjoon_Version::VERSION)
     * base/conjoon/edition - read from application config
     * server/php/max_execution_time - read from php ini
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

        // youtube api key
        $apiKeyPath = $this->_pathToIndex('service/youtube/chromeless', $entries);
        if (!empty($apiKeyPath)) {
            $entries[array_pop($apiKeyPath)]['values'][] = array(
                'name'        => 'api-key',
                'value'       => Zend_Registry::get(
                                     Conjoon_Keys::REGISTRY_CONFIG_OBJECT
                                 )->application->service->youtube->chromeless->apiKey,
                'type'        => 'STRING',
                'is_editable' => 0
            );
        }

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
                $i = 0;
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

}