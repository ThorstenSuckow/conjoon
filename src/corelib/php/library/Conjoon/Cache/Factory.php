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
 * A factory for creating instances of Zend_Cache_Core.
 *
 * @category   Conjoon
 * @package    Conjoon
 * @subpackage Cache
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
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

            case Conjoon_Keys::CACHE_EMAIL_FOLDERS_ROOT_TYPE:
                if (!isset($options['cache']['email']['folders_root_type'])) {
                    return null;
                }
                $cacheOptions = $options['cache']['email']['folders_root_type'];
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