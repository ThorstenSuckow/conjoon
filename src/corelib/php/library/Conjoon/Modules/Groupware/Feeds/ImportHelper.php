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
 * Utility class for access to functionaly related to importing
 * feed items.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Feeds_ImportHelper {


    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private static $_lock = false;

// -------- public api

    /**
     * Returns an array with the feed items as imported from the
     * given $uri.
     *
     * @param string $uri
     * @param integer $requestTimeout
     * @param boolean $useCache
     * @param boolean $useConditionalGet
     *
     * @return array An array with Zend_Feed_Reader_Feed_Interface
     */
    public static function importFeedItems($uri, $requestTimeout = 30, $useCache = false,
        $useConditionalGet = false)
    {
        return self::_processResources(Array(
            'uri'               => $uri,
            'requestTimeout'    => $requestTimeout,
            'useCache'          => $useCache,
            'useConditionalGet' => $useConditionalGet,
            'callback'          => '_importFeedItems'
        ));
    }

    /**
     * Returns the metadata for a feed.
     *
     * @param string  $uri
     * @param integer $requestTimeout
     * @param boolean $useCache
     * @param boolean $useConditionalGet
     *
     * @return array An assoc array with the following key/value pairs:
     * - link
     * - title
     * - description
     */
    public static function getFeedMetadata($uri, $requestTimeout = 30, $useCache = false,
        $useConditionalGet = false)
    {
        return self::_processResources(Array(
            'uri'               => $uri,
            'requestTimeout'    => $requestTimeout,
            'useCache'          => $useCache,
            'useConditionalGet' => $useConditionalGet,
            'callback'          => '_getFeedMetadata'
        ));
    }

    /**
     * Checks whether a given uri points to an rss feed resource.
     *
     * @param string $uri
     *
     * @return boolean true if a rss feed was found, otherwise false.
     */
    public static function isFeedAddressValid($uri)
    {
        /**
         * @see Zend_Feed
         */
        require_once 'Zend/Feed.php';

        try {
            Zend_Feed::import($uri);
            return true;
        } catch (Zend_Feed_Exception $e) {
            // ignore, we failed here
        }

        return false;
    }

    /**
     * Parses all feed items for their values and returns an associative
     * array with their normalized values, along with the accountId specified.
     *
     * @param Zend_Feed_Reader_EntryAbstract $item
     * @param integer $accountId
     *
     * @return array
     */
    public static function normalizeFeedItem(Zend_Feed_Reader_EntryAbstract $item)
    {
        $itemData = array();

        $itemData['title'] = $item->getTitle();

        $authorData = $item->getAuthor();

        $itemData['author']      = $authorData;
        $itemData['authorUri']   = "";
        $itemData['authorEmail'] = "";

        if (is_array($authorData)) {
            if (isset($authorData['name'])) {
                $itemData['author'] = $authorData['name'];
            }

            if (isset($authorData['uri'])) {
                $itemData['authorUri'] = $authorData['uri'];
            }

            if (isset($authorData['email'])) {
                $itemData['authorEmail'] = $authorData['email'];
            }
        }

        // author


         // description
        $itemData['description'] = $item->getDescription();
        if (!$itemData['description']) {
            $itemData['description'] = $itemData['title'];
        }

        // content
        $itemData['content'] = $item->getContent();
        if (!$itemData['content']) {
            $itemData['content'] = $itemData['description'];
        }

        // link
        $itemData['link'] = $item->getLink();

        // guid
        $itemData['guid'] = $item->getId();

        // pubDate
        if (!$item->getDateModified()) {
            // workaround for rss without date
            $itemData['pubDate'] = time();
        } else {

            /**
             * @see Conjoon_Filter_DateToUtc
             */
            require_once 'Conjoon/Filter/DateToUtc.php';

            $toUtcFilter = new Conjoon_Filter_DateToUtc();

            $d = $toUtcFilter->filter(
                     $item->getDateModified()->get(Zend_Date::ISO_8601)
                 );


            $itemData['pubDate'] = $d;
        }

        return $itemData;
    }


// -------- api

    private static function _importFeedItems($uri)
    {
        $import = Zend_Feed_Reader::import($uri);

        $result = array();

        foreach ($import as $item) {
            $result[] = $item;
        }

        return $result;
    }

    private static function _getFeedMetadata($uri)
    {
        $import = Zend_Feed_Reader::import($uri);

        $title = $import->getTitle();
        $link  = $import->getLink();
        if (!$link) {
            $link = $import->getFeedLink();
        }
        $description = $import->getDescription();

        return array(
            'title'       => $title,
            'link'        => $link,
            'description' => $description
        );
    }

    private static function _processResources(Array $config)
    {
        if (self::$_lock) {
            /**
             * @see Conjoon_Log
             */
            require_once 'Conjoon/Log.php';

            Conjoon_Log::log(
                "Conjoon_Modules_Groupware_Feeds_ImportHelper::_processResources "
                . "- possible race condition", Zend_Log::INFO
            );
        }

        self::$_lock = true;

        $uri               = $config['uri'];
        $requestTimeout    = $config['requestTimeout'];
        $useCache          = $config['useCache'];
        $useConditionalGet = $config['useConditionalGet'];
        $callback          = $config['callback'];

        /**
         * @see Zend_Feed_Reader
         */
        require_once 'Zend/Feed/Reader.php';

        if ($useCache !== false) {
            // set the reader's cache here

            /**
             * @see Conjoon_Cache_Factory
             */
            require_once 'Conjoon/Cache/Factory.php';

            /**
             * @see Conjoon_Keys
             */
            require_once 'Conjoon/Keys.php';

            $frCache = Conjoon_Cache_Factory::getCache(
                Conjoon_Keys::CACHE_FEED_READER,
                Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
            );

            if ($frCache) {
                Zend_Feed_Reader::setCache($frCache);
                if ($useConditionalGet !== false) {
                    Zend_Feed_Reader::useHttpConditionalGet();
                }
            }
        }

        Zend_Feed_Reader::getHttpClient()->setConfig(array(
            'timeout' => $requestTimeout
        ));

        $result = self::$callback($uri);

        Zend_Feed_Reader::reset();
        self::$_lock = false;

        return $result;
    }

}