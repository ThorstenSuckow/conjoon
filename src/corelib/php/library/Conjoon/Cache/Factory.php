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
                if (!isset($options['cache']['email']['message'])) {
                    return null;
                }
                $cacheOptions = $options['cache']['email']['message'];
            break;

            case Conjoon_Keys::CACHE_EMAIL_ACCOUNTS:
                if (!isset($options['cache']['email']['accounts'])) {
                    return null;
                }
                $cacheOptions = $options['cache']['email']['accounts'];
            break;

            case Conjoon_Keys::CACHE_FEED_ITEM:
                if (!isset($options['cache']['feed']['item'])) {
                    return null;
                }
                $cacheOptions = $options['cache']['feed']['item'];
            break;

            case Conjoon_Keys::CACHE_FEED_ACCOUNTS:
                if (!isset($options['cache']['feed']['accounts'])) {
                    return null;
                }
                $cacheOptions = $options['cache']['feed']['accounts'];
            break;

            case Conjoon_Keys::CACHE_DB_METADATA:
                if (!isset($options['cache']['db']['metadata'])) {
                    return null;
                }
                $cacheOptions = $options['cache']['db']['metadata'];
            break;

            case Conjoon_Keys::CACHE_TWITTER_ACCOUNTS:
                if (!isset($options['cache']['twitter']['accounts'])) {
                    return null;
                }
                $cacheOptions = $options['cache']['twitter']['accounts'];
            break;

        }

        $frontendOptions = $cacheOptions['frontend'];
        $backendOptions  = $cacheOptions['backend'];

        // check whether the cache-dir for the backend options is relative or
        // absolute. This is a very simple check and may be error-prone,
        // but its okay for now
        $isAbsolute = false;
        if (strpos($backendOptions['cache_dir'], '/') !== 0 &&
            strpos($backendOptions['cache_dir'], ':') !== 1) {
            $backendOptions['cache_dir'] = $options['environment']['application_path'] .
                                           DIRECTORY_SEPARATOR .
                                           $backendOptions['cache_dir'];
        }

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