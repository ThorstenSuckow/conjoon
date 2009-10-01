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
 * Utility class for access to functionaly related to importing
 * feed items.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Feeds_ImportHelper {


    private function __construct()
    {
    }

    private function __clone()
    {
    }

// -------- public api

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
     * @param array Zend_Feed_Reader_Feed_Interface
     * @param integer $accountId
     *
     * @return array
     */
    public static function parseFeedItems($import, $accountId)
    {
        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';

        /**
         * @see Conjoon_Modules_Groupware_Feeds_Item_Filter_Item
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Filter/Item.php';

        $data = array();

        foreach ($import as $item) {

            $itemData = array();
            $itemData['groupwareFeedsAccountsId'] = $accountId;

            $itemData['title'] = $item->getTitle();

            // author
            $itemData['author']      = $item->getAuthor();
            $itemData['authorUri']   = "";//$item->getAuthor(0);
            $itemData['authorEmail'] = "";//$item->getAuthor(0);

             // description
            $itemData['description'] = $item->getDescription();
            if (!$itemData['description']) {
                $itemData['description'] = $itemData['title'];
            }

            // content
            $itemData['content'] = $item->getContent();

            // link
            $itemData['link'] = $item->getLink();

            // guid
            $itemData['guid'] = $item->getId();

            // pubDate
            $itemData['pubDate'] = $item->getDateModified()->getTimestamp();

            $itemData['savedTimestamp'] = time();

            $filter = new Conjoon_Modules_Groupware_Feeds_Item_Filter_Item(
                $itemData,
                Conjoon_Filter_Input::CONTEXT_CREATE
            );
            $fillIn = $filter->getProcessedData();
            Conjoon_Util_Array::underscoreKeys($fillIn);
            $data[] = $fillIn;
        }

        return $data;
    }


// -------- api



}