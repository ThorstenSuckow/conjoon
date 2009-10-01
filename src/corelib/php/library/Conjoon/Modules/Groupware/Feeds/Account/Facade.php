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
     * @var Conjoon_Modules_Groupware_Feeds_Account_Builder
     */
    private $_builder = null;

    /**
     * @var Conjoon_Modules_Groupware_Feeds_Account_Model_Account
     */
    private $_accountModel = null;

    /**
     * @var Conjoon_BeanContext_Decorator _accountDecorator
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
}