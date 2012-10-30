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
 * @see Conjoon_Log
 */
require_once 'Conjoon/Log.php';

/**
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Feeds_Item_Facade {

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Item_Facade $_instance
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Account_Facade $_accountFacade
     */
    private $_accountFacade = null;

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Item_Builder $_builder
     */
    private $_builder = null;

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Item_ListBuilder $_listBuilder
     */
    private $_listBuilder = null;

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Item_Model_Item
     */
    private $_itemModel = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

// -------- public api

    /**
     * Imports and adds the items for the requested feed uri and adds them
     * to the account specified in $accountId if they do not already exist.
     * The account's lastUpdated property will also be set, and the item lists
     * cache will be cleaned, if feed items have been actually added.
     *
     *
     * @param integer $accountId
     * @param integer $userId
     * @param boolean $useReaderCache
     * @param boolean $useConditionalGet
     *
     * @return array Returns an array with the recently added
     * Conjoon_Modules_Groupware_Feeds_Item_Dto
     *
     * @throws Exception
     */
    public function importAndAddFeedItems($accountId, $userId,
        $useReaderCache = false, $useConditionalGet = false)
    {
        $accountId = (int)$accountId;
        $userId    = (int)$userId;

        if ($accountId <= 0 || $userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, accountId was \"$accountId\", "
                . "userId was \"$userId\""
            );
        }

        $accountDto     = $this->_getAccountFacade()->getAccount($accountId, $userId);
        $uri            = $accountDto->uri;
        $requestTimeout = $accountDto->requestTimeout;

         /**
         * @see Conjoon_Modules_Groupware_Feeds_ImportHelper
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/ImportHelper.php';

        // get the feeds metadata
        try {
            $import = Conjoon_Modules_Groupware_Feeds_ImportHelper::importFeedItems(
                $uri, $requestTimeout, $useReaderCache, $useConditionalGet
            );
        } catch (Exception $e) {

            Conjoon_Log::log(
                get_class($this)."::importAndAddFeedItems could not import "
                . "feed items from $uri: \"".get_class($e)."\" - \""
                .$e->getMessage()."\"", Zend_Log::INFO
            );

            // return an empty array, do not delete cache for the account and do not
            // update last_updated timestamp!
            return array();
        }

        /**
         * @see Conjoon_Modules_Groupware_Feeds_Item_Filter_Item
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Filter/Item.php';

        $filter = new Conjoon_Modules_Groupware_Feeds_Item_Filter_Item(
            array(),
            Conjoon_Filter_Input::CONTEXT_CREATE
        );

        $itemResponseFilter = new Conjoon_Modules_Groupware_Feeds_Item_Filter_Item(
            array(),
            Conjoon_Filter_Input::CONTEXT_RESPONSE
        );

        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';

        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        $added  = array();
        $cCache = false;

        foreach ($import as $item) {
            $normalized = Conjoon_Modules_Groupware_Feeds_ImportHelper::normalizeFeedItem(
                    $item
            );
            $normalized['groupwareFeedsAccountsId'] = $accountId;
            $filter->setData($normalized);

            try {
                $fillIn  = $filter->getProcessedData();
            } catch (Zend_Filter_Exception $e) {
                /**
                 * @see Conjoon_Error
                 */
                require_once 'Conjoon/Error.php';

                Conjoon_Log::log(Conjoon_Error::fromFilter($filter, $e), Zend_Log::ERR);
                continue;
            }
            $dtoData = $fillIn;

            Conjoon_Util_Array::underscoreKeys($fillIn);

            $isAdded = $this->_addItemIfNotExists($fillIn, $accountId, false);

            if ($isAdded > 0) {
                $cCache = true;
                $dtoData['id']   = $isAdded;
                $dtoData['name'] = $accountDto->name;

                $itemResponseFilter->setData($dtoData);
                $dtoData = $itemResponseFilter->getProcessedData();

                $added[] = Conjoon_BeanContext_Inspector::create(
                    'Conjoon_Modules_Groupware_Feeds_Item', $dtoData
                )->getDto();
            }
        }

        if ($cCache) {
            $this->_removeListCacheForAccountIds(array($accountId));
        }

        $this->_getAccountFacade()->setLastUpdated(array($accountId), time());

        return $added;
    }
    /**
     * Deletes all feed items for the specified account id.
     *
     * @param integer $accountId
     *
     * @throws InvalidArgumentException
     */
    public function deleteFeedItemsForAccountId($accountId)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, accountId was \"$accountId\""
            );
        }

        $affected = $this->_getItemModel()->deleteItemsForAccount($accountId);

        $this->_removeListCacheForAccountIds(array($accountId));
        $this->_getBuilder()->cleanCacheForTags(array(
            'accountId' => $accountId
        ));
    }

    /**
     * Sets the items for the specified ids either to read
     * or unread.
     *
     * @param array integer $read
     * @param array integer$unread
     *
     */
    public function setItemsRead(Array $read, Array $unread)
    {
        /**
         * @see Conjoon_Filter_PositiveArrayValues
         */
        require_once 'Conjoon/Filter/PositiveArrayValues.php';

        $filter = new Conjoon_Filter_PositiveArrayValues();

        $wlistRead   = $filter->filter($read);
        $wlistUnread = $filter->filter($unread);

        $feedIds = array();

        if (!empty($wlistRead)) {
            $changed = $this->_getItemModel()->setItemRead($wlistRead, true);
            if ($changed > 0) {
                $feedIds = $wlistRead;
            }
        }

        if (!empty($wlistUnread)) {
            $changed = $this->_getItemModel()->setItemRead($wlistUnread, false);
            if ($changed > 0) {
                $feedIds = array_merge($feedIds, $wlistUnread);
            }
        }

        $this->_removeListCacheForFeedIds($feedIds);
    }

    /**
     * returns a list of items for the specified account id.
     *
     * @param integer $accountId
     *
     * @return array Conjoon_Modules_Groupware_Feeds_Item_Dto
     *
     * @throws InvalidArgumentException
     */
    public function getItemsForAccount($accountId)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, accountId was \"$accountId\""
            );
        }

        return $this->_getListBuilder()->get(
            array('accountId' => $accountId)
        );
    }

    /**
     * Deletes all feed items for the specified user based on the
     * deleteInterval.
     *
     * @param integer $userId
     *
     * @throws InvalidArgumentException
     */
    public function deleteOldFeedItems($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, userId was \"$userId\""
            );
        }

        $model = $this->_getItemModel();

        $toDelete = $model->getFeedItemIdsToDelete($userId);

        $this->_removeListCacheForFeedIds($toDelete);

        $model->deleteFeedItemsForIds($toDelete);
    }

    /**
     * Returns an array of Conjoon_Modules_Groupware_Feeds_Item_Dto
     * and also reads out new feed items and returns them along with
     * items out of the data storage.
     * If $removeold is set to true, the emthod will alos delete old
     * feed items out of the data storage.
     *
     * @param integer $userId
     * @param boolean $removeold
     * @param integer $timeout The timeout for the http connection that is
     * responsible for querying accounts for new feed items. Depending on
     * the number of accounts that have to be queried, this should be set to
     * a high enough value.
     *
     * @return array Conjoon_Modules_Groupware:Feeds_Item_Dto
     */
    public function syncAndGetFeedItemsForUser($userId, $removeOld, $timeout)
    {
        /**
         * @see Conjoon_Modules_Groupware_Feeds_ImportHelper
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/ImportHelper.php';

        $time     = time();
        $accounts = $this->_getAccountFacade()->getAccountsToUpdate($userId, $time);

        Conjoon_Log::log(count($accounts). " need to be updated at ".date("Y-m-d H:i:s"), Zend_Log::INFO);

        $insertedItems   = array();
        $len             = count($accounts);

        $secTimeout = $timeout/1000;
        $defTimeout = -1;
        // compute the timeout for the connections. Filter should have set the default
        // timeout to 30000 ms if the timeout param was not submitted
        // we need to compare this with the max_execution_time of the php installation
        // and take action in case the requestTimeout exceeds it, so each account will have
        // a reduced timeout, just in case (the configured timeout won't be considered then)
        if ($len > 0 && $secTimeout >= ini_get('max_execution_time')) {
            $defTimeout = (int)round(ini_get('max_execution_time')/$len);

            // if $defTimeout is less than 1, we will not try to load any feeds, or else
            // no response will ge through to the client
            if ($defTimeout < 1) {
                $len = 0;
            }
        }

        for ($i = 0; $i < $len; $i++) {

            Conjoon_Log::log($accounts[$i]->name. " needs to be queried at ".date("Y-m-d H:i:s"), Zend_Log::INFO);

            // set requestTimeout to default if necessary
            if ($defTimeout != -1) {
                $accounts[$i]->requestTimeout = $defTimeout;
            }
            try {
                $fetched = $this->importAndAddFeedItems(
                    $accounts[$i]->id,
                    $userId,
                    true,
                    true
                );

                $insertedItems = array_merge($insertedItems, $fetched);

                Conjoon_Log::log($accounts[$i]->name. " has been updated with ".count($fetched)." items", Zend_Log::INFO);

            } catch (Exception $e) {
                throw $e;
            }
        }

        if ($removeOld) {
            $this->deleteOldFeedItems($userId);
            $items = $this->getFeedItemsForUser($userId);
        } else {
            // return all items that where added during this request
            $items = $insertedItems;
        }

        return $items;
    }

    /**
     * Returns an Conjoon_Modules_Groupware_Feeds_Item_Dto
     * for the specified item id and the specified accountId.
     *
     * @param integer $id
     * @param integer $accountId
     * @param integer $userId
     *
     * @return Conjoon_Modules_Groupware_Feeds_Item_Dto
     *
     * @throws InvalidArgumentExceptions
     */
    public function getFeedContent($id, $accountId, $userId)
    {
        $id        = (int)$id;
        $accountId = (int)$accountId;

        if ($id <= 0 || $accountId <= 0) {
            throw new InvalidArgumentException(
                  "Invalid arguments: id was \"$id\", "
                . "accountId was \"$accountId\""
            );
        }

        $isImageEnabled = $this->_getAccountFacade()->getAccount($accountId, $userId)
                          ->isImageEnabled;

        return $this->_getBuilder()->get(
            array(
                'id'             => $id,
                'accountId'      => $accountId,
                'isImageEnabled' => $isImageEnabled
            )
        );
    }

    /**
     * Returns an array of feed items for all accounts that belong
     * to the specified user.
     *
     * @param integer $userId
     *
     * @return array Conjoon_Modules_Groupware_Feeds_Item_Dto
     *
     * @throws InvalidArgumentExceptions
     */
    public function getFeedItemsForUser($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                  "Invalid arguments: userId was \"$userId\""
            );
        }

        return $this->_getFeedItems($userId, 0);
    }

    /**
     * Returns an array of feed items for the specified account id.
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return array Conjoon_Modules_Groupware_Feeds_Item_Dto
     *
     * @throws InvalidArgumentExceptions
     */
    public function getFeedItemsForAccount($accountId, $userId)
    {
        $userId    = (int)$userId;
        $accountId = (int)$accountId;

        if ($userId <= 0 || $accountId <= 0) {
            throw new InvalidArgumentException(
                  "Invalid arguments: userId was \"$userId\", "
                . "accountId was \"$accountId\""
            );
        }

        return $this->_getFeedItems($userId, $accountId);
    }


// -------- api

    /**
     *
     * @return Conjoon_Builder
     */
    private function _getListBuilder()
    {
        if (!$this->_listBuilder) {
            /**
             * @see Conjoon_Keys
             */
            require_once 'Conjoon/Keys.php';

            /**
             * @see Conjoon_Builder_Factory
             */
            require_once 'Conjoon/Builder/Factory.php';

            $this->_listBuilder = Conjoon_Builder_Factory::getBuilder(
                Conjoon_Keys::CACHE_FEED_ITEMLIST,
                Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray(),
                $this->_getItemModel()
            );
        }

        return $this->_listBuilder;
    }

    /**
     *
     * @return Conjoon_Builder
     */
    private function _getBuilder()
    {
        if (!$this->_builder) {
            /**
             * @see Conjoon_Keys
             */
            require_once 'Conjoon/Keys.php';

            /**
             * @see Conjoon_Builder_Factory
             */
            require_once 'Conjoon/Builder/Factory.php';

            $this->_builder = Conjoon_Builder_Factory::getBuilder(
                Conjoon_Keys::CACHE_FEED_ITEM,
                Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray(),
                $this->_getItemModel()
            );
        }

        return $this->_builder;
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Feeds_Account_Facade
     */
    private function _getAccountFacade()
    {
        if (!$this->_accountFacade) {
            /**
             * @see Conjoon_Modules_Groupware_Feeds_Account_Facade
             */
            require_once 'Conjoon/Modules/Groupware/Feeds/Account/Facade.php';

            $this->_accountFacade =
                Conjoon_Modules_Groupware_Feeds_Account_Facade::getInstance();
        }

        return $this->_accountFacade;
    }

    /**
     * Returns the feed items for the user and the specified account.
     * If the account is not specified, all feed items for all accounts
     * for the user will be returned.
     *
     * @param integer $userId
     * @param integer $accountId
     *
     * @return Conjoon_Modules_Groupware_Feeds_Item_Dto
     */
    private function _getFeedItems($userId, $accountId)
    {
        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $accountModel = $this->_getAccountFacade();

        if ($accountId === 0) {
            $data = $accountModel->getAccountsForUser($userId);
        } else {
            $data = array($accountModel->getAccount($accountId, $userId));
        }

        $items    = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $tmpItems = $this->getItemsForAccount($data[$i]->id);
            $items = array_merge($items, $tmpItems);
        }

        return $items;
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Feeds_Item_Model_Item
     */
    private function _getItemModel()
    {
        if (!$this->_itemModel) {
             /**
             * @see Conjoon_Modules_Groupware_Feeds_Item_Model_Item
             */
            require_once 'Conjoon/Modules/Groupware/Feeds/Item/Model/Item.php';

            $this->_itemModel = new Conjoon_Modules_Groupware_Feeds_Item_Model_Item();
        }

        return $this->_itemModel;
    }

    /**
     * Removes the cache for the account ids that own the specified
     * feed ids.
     *
     * @param array $feedIds
     */
    private function _removeListCacheForFeedIds(Array $feedIds)
    {
        /**
         * @see Conjoon_Filter_PositiveArrayValues
         */
        require_once 'Conjoon/Filter/PositiveArrayValues.php';

        $filter = new Conjoon_Filter_PositiveArrayValues();

        $feedIds = $filter->filter($feedIds);

        $accountIds = $this->_getAccountIdsForFeedIds($feedIds);

        $this->_removeListCacheForAccountIds($accountIds);
    }

    /**
     * Removes the cache for the specified account ids.
     *
     * @param array $accountIds
     */
    private function _removeListCacheForAccountIds(Array $accountIds)
    {
        /**
         * @see Conjoon_Filter_PositiveArrayValues
         */
        require_once 'Conjoon/Filter/PositiveArrayValues.php';

        $filter = new Conjoon_Filter_PositiveArrayValues();

        $accountIds = $filter->filter($accountIds);

        $builder = $this->_getListBuilder();
        for ($i = 0, $len = count($accountIds); $i < $len; $i++) {
            $builder->remove(array('accountId' => $accountIds[$i]));
        }
    }

    /**
     * Returns the account ids for the passed feed ids.
     *
     * @param array integer $feedIds
     *
     * @return array
     */
    private function _getAccountIdsForFeedIds(Array $feedIds)
    {
        /**
         * @see Conjoon_Filter_PositiveArrayValues
         */
        require_once 'Conjoon/Filter/PositiveArrayValues.php';

        $filter = new Conjoon_Filter_PositiveArrayValues();

        $feedIds = $filter->filter($feedIds);

        if (empty($feedIds)) {
            return array();
        }

        $model = $this->_getItemModel();

        return $model->getAccountIdsForFeedIds($feedIds);
    }

    /**
     * Adds a feed item into the data storage for the specified account, if
     * it does not already exist.
     *
     * @param array   $data
     * @param integer $accountId
     * @param boolean $clearCache Whether or not to clear the feedlist cache for the
     * corresponding account. Cache will be cleaed if the item was actually added.
     *
     * @return integer The id of the added item as provided by the data storage
     *
     * @throws InvalidArgumentException
     */
    private function _addItemIfNotExists(Array $data, $accountId, $clearCache = true)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, accountId was \"$accountId\""
            );
        }

        $id = $this->_getItemModel()->addItemIfNotExists($data, $accountId);

        if ($clearCache === true) {
            $this->_removeListCacheForAccountIds(array($accountId));
        }

        return $id;
    }

}