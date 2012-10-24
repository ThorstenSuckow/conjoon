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

            case Conjoon_Keys::CACHE_FEED_ITEMLIST:
                if (!isset($options['cache']['feed']['item_list'])) {
                    return null;
                }
                $cacheOptions = $options['cache']['feed']['item_list'];
            break;

            case Conjoon_Keys::CACHE_FEED_ACCOUNT:
                if (!isset($options['cache']['feed']['account'])) {
                    return null;
                }
                $cacheOptions = $options['cache']['feed']['account'];
            break;

            case Conjoon_Keys::CACHE_FEED_ACCOUNTLIST:
                if (!isset($options['cache']['feed']['account_list'])) {
                    return null;
                }
                $cacheOptions = $options['cache']['feed']['account_list'];
            break;

            case Conjoon_Keys::CACHE_FEED_READER:
                if (!isset($options['cache']['feed']['reader'])) {
                    return null;
                }
                $cacheOptions = $options['cache']['feed']['reader'];
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

        /**
         * @see Zend_Cache
         */
        require_once 'Zend/Cache.php';

        return Zend_Cache::factory(
            $cacheOptions['frontend_type'],
            $cacheOptions['backend_type'],
            $cacheOptions['frontend'],
            $cacheOptions['backend']
        );


    }

}