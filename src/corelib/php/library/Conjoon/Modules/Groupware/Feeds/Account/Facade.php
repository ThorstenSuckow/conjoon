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
 * @see Conjoon_Log
 */
require_once 'Conjoon/Log.php';

/**
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Feeds_Account_Facade {

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Account_Facade
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Account_ListBuilder
     */
    private $_listBuilder = null;

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Account_Filter_Account
     */
    private $_updateAccountFilter = null;

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Item_Facade
     */
    private $_itemFacade = null;

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Account_Builder
     */
    private $_builder = null;

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Account_Model_Account
     */
    private $_accountModel = null;

    /**
     * @var Conjoon_BeanContext_Decorator $_accountDecorator
     */
    private $_accountDecorator = null;

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
     * Adds an account to the data storage.
     *
     * Fields required in $data:
     *  - deleteInterval
     *  - name
     *  - requestTimeout
     *  - updateInterval
     *  - uri
     *
     *
     * @param Array $data
     * @param integer $userId
     *
     * @return Conjoon_Modules_Groupware_Feeds_Account_Dto The recently added
     * account, or null if not created
     *
     * @throws Exception
     */
    public function addAccount(Array $data, $userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, userId was \"$userId\""
            );
        }

        /**
         * @see Conjoon_Modules_Groupware_Feeds_Account_Filter_Account
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Account/Filter/Account.php';

        $filter = new Conjoon_Modules_Groupware_Feeds_Account_Filter_Account(
            $data, Conjoon_Filter_Input::CONTEXT_CREATE
        );

        $data    = $filter->getProcessedData();
        $dtoData = $data;

        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';

        Conjoon_Util_Array::underscoreKeys($data);

        $result       = $this->_getAccountModel()->addAccount($data, $userId);
        $addedAccount = null;

        if ($result > 0) {
            $dtoData['id'] = $result;

            /**
             * @see Conjoon_BeanContext_Inspector
             */
            require_once 'Conjoon/BeanContext/Inspector.php';

            $addedAccount = Conjoon_BeanContext_Inspector::create(
                'Conjoon_Modules_Groupware_Feeds_Account', $dtoData
            )->getDto();

            $this->_getListBuilder()->cleanCacheForTags(array(
                'userId' => $userId
            ));
        }

        return $addedAccount;
    }

    /**
     * Adds a feed account to the data storage for the specified
     * user in $userId.
     *
     * Fields required in $data:
     *  - deleteInterval
     *  - name
     *  - requestTimeout
     *  - updateInterval
     *  - uri
     *
     * @param Array $data
     * @param intteger $userId
     *
     * @throws Exception
     */
    public function addAccountAndImport(Array $data, $userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, userId was \"$userId\""
            );
        }

        /**
         * @see Conjoon_Modules_Groupware_Feeds_ImportHelper
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/ImportHelper.php';

        $data['lastUpdated'] = time();

        // get the feeds metadata
        $metaData = Conjoon_Modules_Groupware_Feeds_ImportHelper
                  ::getFeedMetaData($data['uri'], $data['requestTimeout'], true, true);

        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';

        Conjoon_Util_Array::applyIf($data, $metaData);

        $addedAcount = $this->addAccount($data, $userId, true);

        // something failed. Return.
        if ($addedAcount == null) {
            return array();
        }

        // get the feed items now and insert them!
        $items = $this->_getItemFacade()->importAndAddFeedItems(
            $addedAcount->id, $userId, true, false
        );

        return array(
            'account' => $addedAcount,
            'items'   => $items
        );
    }

    /**
     * Removes all the accounts for the specified account ids, for the user
     * with the specified $userId.
     *
     * @param array $accountIds
     * @param integer $userId
     *
     * @return array An array with the ids that got successfully removed
     *
     * @throws InvalidArgumentException
     */
    public function removeAccountsForIds(Array $accountIds, $userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, accountId was \"$accountId\""
            );
        }

        /**
         * @see Conjoon_Filter_PositiveArrayValues
         */
        require_once 'Conjoon/Filter/PositiveArrayValues.php';

        $filter = new Conjoon_Filter_PositiveArrayValues();

        $accountIds = $filter->filter($accountIds);

        $removed = array();
        for ($i = 0, $len = count($accountIds); $i < $len; $i++) {
            if ($this->removeAccountForId($accountIds[$i], $userId) === true) {
                $removed[] = $accountIds[$i];
            }
        }

        return $removed;
    }

    /**
     * Removes the account as specified in $accountId for the user
     * with the specified $userId.
     *
     * @param integer $accountId
     * @param integer $userId
     *
     * @return boolean true if the account was removed, otherwise false
     *
     * @throws InvalidArgumentException
     */
    public function removeAccountForId($accountId, $userId)
    {
        $accountId = (int)$accountId;
        $userId    = (int)$userId;

        if ($accountId <= 0 || $userId <= 0) {
            throw new InvalidArgumentException(
                  "Invalid argument supplied, accountId was \"$accountId\", "
                . "userId was \"$userId\""
            );
        }

        $affected = $this->_getAccountModel()->deleteAccount($accountId, false);

        if ($affected !== false) {
            $this->_getBuilder()->remove(array('accountId' => $accountId));
            $this->_getListBuilder()->cleanCacheForTags(array('userId' => $userId));
            $this->_getItemFacade()->deleteFeedItemsForAccountId($accountId);
        }

        return $affected;
    }

    /**
     * Updates the account data in the data storage with the account-data
     * specified in $data.
     *
     * @param array a numeric array where each value holds data to update
     * for an account
     * @param integer $userId The id of the user the accounts belong to
     *
     * @return array a list with ids of successfully updated accounts
     *
     * @throws InvalidArgumentException
     */
    public function updateAccounts(Array $data, $userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                  "Invalid argument supplied, userId was \"$userId\""
            );
        }

        $updated = array();

        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $id = $data[$i]['id'];
            try {
                if ($this->updateAccount($id, $data[$i], $userId) === true) {
                    $updated[] = $id;
                }
            } catch (Exception $e) {
                Conjoon_Log::log($e, Zend_Log::ERR);
            }
        }

        return $updated;
    }

    /**
     * Updates a single account with the data from $data for the specified
     * $userId.
     *
     * @param integer $accountId the id of the account to update.
     * @param Array $data
     * @param integer $userId
     *
     * @return boolean true, if updating the account was successfull, otherwise
     * false
     *
     * @throws Exception
     */
    public function updateAccount($accountId, Array $data, $userId)
    {
        $accountId = (int)$accountId;
        $userId    = (int)$userId;

        if ($userId <= 0 || $accountId <= 0) {
            throw new InvalidArgumentException(
                  "Invalid argument supplied, userId was \"$userId\", "
                . "accountId was \"$accountId\""
            );
        }

        $filter = $this->_getUpdateAccountFilter();
        $filter->setData($data);

        try {
            $data = $filter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            /**
             * @see Conjoon_Error
             */
            require_once 'Conjoon/Error.php';

            Conjoon_Log::log(Conjoon_Error::fromFilter($filter, $e), Zend_Log::ERR);
            return false;
        }

        if (array_key_exists('id', $data)) {
            unset($data['id']);
        }

        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';

        Conjoon_Util_Array::underscoreKeys($data);

        $affected = $this->_getAccountModel()->updateAccount($accountId, $data);

        if ($affected === true) {
            $this->_getBuilder()->remove(array('accountId' => $accountId));
            $this->_getListBuilder()->cleanCacheForTags(array('userId' => $userId));
        }

        return $affected === true ? true : false;

    }

    /**
     * Sets the last updated timestamp for the specified account ids
     * to $time.
     *
     * @param array $accountIds
     * @param integer $time
     *
     */
    public function setLastUpdated($accountIds, $time)
    {
        $this->_getAccountModel()->setLastUpdated($accountIds, $time);
    }


    /**
     * Returns all accounts for the specified $userId as
     * Conjoon_Modules_Groupware_Feeds_Account_Dto
     * which updateInterval is exceeded based on the passed unix timestamp
     * $time.
     *
     * @param integer $userId
     * @param integer $time
     *
     * @return array Conjoon_Modules_Groupware_Feeds_Account_Dto
     */
    public function getAccountsToUpdate($userId, $time)
    {
        return $this->_getAccountDecorator()->getAccountsToUpdateAsDto(
            $userId, $time
        );
    }

    /**
     * Returns the Conjoon_Modules_Groupware_Feeds_Account_Dto for the specified
     * account id.
     *
     * @param integer $accountId
     *
     * @return Conjoon_Modules_Groupware_Feeds_Account_Dto
     *
     * @throws InvalidArgumentExceptions
     */
    public function getAccount($accountId, $userId)
    {
        $userId    = (int)$userId;
        $accountId = (int)$accountId;

        if ($userId <= 0 || $accountId <= 0) {
            throw new InvalidArgumentException(
                    "Invalid arguments: userId was \"$userId\", "
                  . " accountId was \"$accountId\""
            );
        }

        return $this->_getBuilder()->get(
            array('userId' => $userId, 'accountId' => $accountId)
        );
    }

    /**
     * Returns an array with all Feed Accounts for the specified user id.
     *
     * @param integer $userId
     *
     * @return array Conjoon_Modules_Groupware_Feeds_AccountDto
     *
     * @throws InvalidArgumentExceptions
     */
    public function getAccountsForUser($userId)
    {
        $userId    = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                    "Invalid arguments: userId was \"$userId\""
            );
        }

        return $this->_getListBuilder()->get(
            array('userId' => $userId)
        );
    }


// -------- api

    /**
     *
     * @return Conjoon_BeanContext_Decorator
     */
    private function _getAccountDecorator()
    {
        if (!$this->_accountDecorator) {

            /**
             * @see Conjoon_BeanContext_Decorator
             */
            require_once 'Conjoon/BeanContext/Decorator.php';

            $this->_accountDecorator = new Conjoon_BeanContext_Decorator(
                $this->_getAccountModel()
            );

        }

        return $this->_accountDecorator;

    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Feeds_Account_ListBuilder
     */
    private function _getListBuilder()
    {
        if (!$this->_listBuilder) {
            /**
             * @see Conjoon_Builder_Factory
             */
            require_once 'Conjoon/Builder/Factory.php';

            /**
             * @see Conjoon_Keys
             */
            require_once 'Conjoon/Keys.php';

            $this->_listBuilder = Conjoon_Builder_Factory::getBuilder(
                Conjoon_Keys::CACHE_FEED_ACCOUNTLIST,
                Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray(),
                $this->_getAccountModel()
            );
        }

        return $this->_listBuilder;
    }

    /**
     * @return Conjoon_Modules_Groupware_Feeds_Account_Builder
     */
    private function _getBuilder()
    {
        if (!$this->_builder) {
            /**
             * @see Conjoon_Builder_Factory
             */
            require_once 'Conjoon/Builder/Factory.php';

            /**
             * @see Conjoon_Keys
             */
            require_once 'Conjoon/Keys.php';

            $this->_builder = Conjoon_Builder_Factory::getBuilder(
                Conjoon_Keys::CACHE_FEED_ACCOUNT,
                Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray(),
                $this->_getAccountModel()
            );
        }

        return $this->_builder;
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Feeds_Account_Model_Account
     */
    private function _getAccountModel()
    {
        if (!$this->_accountModel) {
             /**
             * @see Conjoon_Modules_Groupware_Feeds_Account_Model_Account
             */
            require_once 'Conjoon/Modules/Groupware/Feeds/Account/Model/Account.php';

            $this->_accountModel = new Conjoon_Modules_Groupware_Feeds_Account_Model_Account();
        }

        return $this->_accountModel;
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Feeds_Item_Facade
     */
    private function _getItemFacade()
    {
        if (!$this->_itemFacade) {

            /**
             * @see Conjoon_Modules_Groupware_Feeds_Item_Facade
             */
            require_once 'Conjoon/Modules/Groupware/Feeds/Item/Facade.php';

            $this->_itemFacade = Conjoon_Modules_Groupware_Feeds_Item_Facade
                                 ::getInstance();
        }

        return $this->_itemFacade;
    }

    /**
     *
     * @see Conjoon_Modules_Groupware_Feeds_Account_Filter_Account
     */
    private function _getUpdateAccountFilter()
    {
        if (!$this->_updateAccountFilter) {

            /**
             * @see Conjoon_Modules_Groupware_Feeds_Account_Filter_Account
             */
            require_once 'Conjoon/Modules/Groupware/Feeds/Account/Filter/Account.php';

            $this->_updateAccountFilter = new Conjoon_Modules_Groupware_Feeds_Account_Filter_Account(
                array(),
                Conjoon_Filter_Input::CONTEXT_UPDATE
            );
        }

        return $this->_updateAccountFilter;

    }


}