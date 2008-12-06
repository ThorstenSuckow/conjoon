<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: User.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild_rep/trunk/src/corelib/php/library/Intrabuild/Modules/Default/User.php $
 */


/**
 * A static class that grants access to application wide settings.
 *
 * @category   Intrabuild
 * @package    Intrabuild
 * @subpackage Registry
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Default_Registry {

    private static $_data = null;

    private static function _init()
    {
        /**
         * @see Intrabuild_Version
         */
        require_once 'Intrabuild/Version.php';

        /**
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        /**
         * @see Intrabuild_Keys
         */
        require_once 'Intrabuild/Keys.php';

        self::$_data = array(
            'base' => array(
                'conjoon' => array(
                    'name'    => 'conjoon',
                    'version' => Intrabuild_Version::VERSION,
                    'edition' => Zend_Registry::get(
                        Intrabuild_Keys::REGISTRY_CONFIG_OBJECT
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