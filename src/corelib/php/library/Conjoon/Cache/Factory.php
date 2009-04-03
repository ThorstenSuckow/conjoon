<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * A factory for creating instances of Zend_Cache_Core.
 *
 * @category   Conjoon
 * @package    Conjoon
 * @subpackage Cache
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Cache_Factory {

    /**
     * Convinient method to create and return objects of the type Zend_Cache_Core.
     *
     *
     * @param string $key The key used to determine which cache options to use
     * @param array $options A set of options with which the needed cache objects
     * can be created. Basically, the needed keys follow the configuration properties
     * used in the config.ini.php for the Conjoon project.
     *
     * @return Conjoon_Builder
     */
    public static function getCache($key, Array $options)
    {
        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $cacheOptions = array();

        switch ($key) {
            case Conjoon_Keys::CACHE_EMAIL_MESSAGE:
                $cacheOptions = $options['cache']['email']['message'];
            break;

            case Conjoon_Keys::CACHE_FEED_ITEM:
                $cacheOptions = $options['cache']['feed']['item'];
            break;

            case Conjoon_Keys::CACHE_DB_METADATA:
                $cacheOptions = $options['cache']['db']['metadata'];
            break;
        }

        $appPath  = $options['environment']['application_path'];
        $cacheDir = $options['environment']['cache_dir'];

        $frontendOptions = $cacheOptions['frontend'];
        $backendOptions  = $cacheOptions['backend'];

        $backendOptions['cache_dir'] = $appPath  . '/' .
                                       $cacheDir . '/' .
                                       $backendOptions['cache_dir'];

        $frontendType = $cacheOptions['frontend_type'];
        $backendType  = $cacheOptions['backend_type'];

        /**
         * @see Zend_Cache
         */
        require_once 'Zend/Cache.php';

        return Zend_Cache::factory(
            $frontendType,
            $backendType,
            $frontendOptions,
            $backendOptions
        );


    }

}