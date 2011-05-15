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
 * A static class that grants access to application wide settings.
 *
 * @category   Conjoon
 * @package    Conjoon
 * @subpackage Registry
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Default_Registry {

    private static $_data = null;

    private static function _init()
    {
        /**
         * @see Conjoon_Version
         */
        require_once 'Conjoon/Version.php';

        /**
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        self::$_data = array(
            'client' => array(
                'environment' => array(
                    'device' => (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'ipod')
                                || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'iphone'))
                                ? 'iphone'
                                : 'default'
                ),
                'system' => array(
                    'sfx' => array(
                        'enabled' => true
                    )
                )
            ),
            'server' => array(
                'php' => array(
                    'max_execution_time' => ini_get('max_execution_time')
                ),
                'environment' => array(
                    'protocol' => ((stripos($_SERVER['SERVER_PROTOCOL'], 'https') === false)
                                  ? 'http'
                                  : 'https'),
                    'host'     => $_SERVER['SERVER_NAME'],
                    'port'     => $_SERVER['SERVER_PORT']
                )
            ),
            'base' => array(
                'conjoon' => array(
                    'name'    => 'conjoon demo',
                    'version' => Conjoon_Version::VERSION,
                    'edition' => Zend_Registry::get(
                        Conjoon_Keys::REGISTRY_CONFIG_OBJECT
                    )->environment->edition
                )
            )
        );
    }

    /**
     * Constructor.
     * Enforce static behavior.
     */
    private function __construct()
    {

    }

    /**
     * Returns a value for the key. Returns null if the key was not found.
     * The key must be in the format "/key1/key2/key3".
     * The path to a value must be fully supplied, as subtrees won't be returned
     * by the registry.
     *
     * @param {String} $key
     *
     * @return mixed
     */
    public static function get($key)
    {
        if (!self::$_data) {
            self::_init();
        }
        $key = trim($key, '/');

        $path = explode('/', $key);

        if (empty($path)) {
            return null;
        }

        $val = self::$_data[$path[0]];
        for ($i = 1, $len = count($path); $i < $len; $i++) {
            $val = $val[$path[$i]];
        }

        if (is_array($val)) {
            return null;
        }

        return $val;
    }

    /**
     * Returns the complete registry as an associative array.
     *
     * @return array
     */
    public static function getAll()
    {
        if (!self::$_data) {
            self::_init();
        }

        return self::$_data;
    }


}