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

        if (!empty($wlistRead)) {
            $this->_getItemModel()->setItemRead($wlistRead, true);
        }

        if (!empty($wlistUnread)) {
            $this->_getItemModel()->setItemRead($wlistUnread, false);
        }
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
                "Invalid argument supplied, accountId was \"$accountId\""
            );
        }

        $this->_getItemModel()->deleteOldFeedItems($userId);
    }



    /**
     * Adds a feed item into the data storage for the specified account, if
     * it does not already exist.
     *
     * @param array   $data
     * @param integer $accountId
     *
     * @return integer The id of the added item as provided by the data storage
     *
     * @throws InvalidArgumentException
     */
    public function addItemIfNotExists(Array $data, $accountId)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, accountId was \"$accountId\""
            );
        }

        return $this->_getItemModel()->addItemIfNotExists($data, $accountId);
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
         * @see Conjoon_Modules_Groupware_Feeds_Item_Filter_Item
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Filter/Item.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        /**
         * @see Conjoon_Cache_Factory
         */
        require_once 'Conjoon/Cache/Factory.php';

        /**
         * @see Zend_Feed_Reader
         */
        require_once 'Zend/Feed/Reader.php';

        /**
         * @see Conjoon_Modules_Groupware_Feeds_ImportHelper
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/ImportHelper.php';

        $time     = time();
        $accounts = $this->_getAccountFacade()->getAccountsToUpdate($userId, $time);

        $itemResponseFilter = new Conjoon_Modules_Groupware_Feeds_Item_Filter_Item(
            array(),
            Conjoon_Filter_Input::CONTEXT_RESPONSE
        );

        $updatedAccounts = array();
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

        // set the reader's cache here
        $frCache = Conjoon_Cache_Factory::getCache(
            Conjoon_Keys::CACHE_FEED_READER,
            Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
        );

        if ($frCache) {
            Zend_Feed_Reader::setCache($frCache);
        }

       for ($i = 0; $i < $len; $i++) {
            // set requestTimeout to default if necessary
            if ($defTimeout != -1) {
                $accounts[$i]->requestTimeout = $defTimeout;
            }
            try {
                // set the client for each account so it can be configured
                // with the timeout. In case the sum of all timeouts exceeds
                // the max_execution_time of the PHP installation, each
                // request will be configured with the same timeout so the script
                // has enough time to finish
                Zend_Feed_Reader::setHttpClient(new Zend_Http_Client(
                    null, array('timeout' => $accounts[$i]->requestTimeout - 2)
                ));

                $import = Zend_Feed_Reader::import($accounts[$i]->uri);

                $items = Conjoon_Modules_Groupware_Feeds_ImportHelper::parseFeedItems(
                    $import, $accounts[$i]->id
                );

                for ($a = 0, $lena = count($items); $a < $lena; $a++) {
                    $items[$a]['saved_timestamp'] = time();
                    $added = $this->addItemIfNotExists($items[$a], $accounts[$i]->id);
                    if ($added !== 0 && !$removeOld) {
                        $items[$a]['name'] = $accounts[$i]->name;
                        $items[$a]['id']   = $added;
                        Conjoon_Util_Array::camelizeKeys($items[$a]);
                        $itemResponseFilter->setData($items[$a]);
                        $insertedItems[] = $itemResponseFilter->getProcessedData();
                    }
                }

                // only mark as updated if no exception occurred
                $updatedAccounts[$accounts[$i]->id] = true;

            } catch (Exception $e) {
                // ignore
            }
        }

        // reset Zend_Feed_Reader
        Zend_Feed_Reader::reset();

        // set the last updated timestamp for the accounts
        if (!empty($updatedAccounts)) {
            $this->_getAccountFacade()->setLastUpdated(
                array_keys($updatedAccounts), $time
            );
        }

        if ($removeOld) {
            $this->deleteOldFeedItems($userId);;
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

}