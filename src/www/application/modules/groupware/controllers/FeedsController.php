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
 * @see Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

/**
 * @see Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';


class Groupware_FeedsController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('is.feed.address.valid', self::CONTEXT_JSON)
                       ->addActionContext('get.feed.items', self::CONTEXT_JSON)
                       ->addActionContext('get.feed.accounts', self::CONTEXT_JSON)
                       ->addActionContext('set.item.read', self::CONTEXT_JSON)
                       ->addActionContext('add.feed', self::CONTEXT_JSON)
                       ->addActionContext('update.accounts', self::CONTEXT_JSON)
                       ->addActionContext('get.feed.content', self::CONTEXT_JSON)
                       ->initContext();

        $this->_helper->filterRequestData()
                      ->registerFilter('Groupware_FeedsController::get.feed.items')
                      ->registerFilter('Groupware_FeedsController::set.item.read')
                      ->registerFilter('Groupware_FeedsController::get.feed.content')
                      ->registerFilter('Groupware_FeedsController::update.accounts')
                      ->registerFilter('Groupware_FeedsController::is.feed.address.valid')
                      ->registerFilter('Groupware_FeedsController::add.account');
    }

// -------- items

    /**
     * Returns all feed items out of the database belonging to the current user,
     * and does also query all accounts for new feed items.
     * Feed items usually won't have a feed body.
     * On each manual refresh of the store and on the first startup of the store,
     * the client sends the parameter "removeold" set to "true", which tells the model
     * to wipe all old feed entries out of the database, based on the configured
     * "deleteInterval"-property in the according account.
     *
     */
    public function getFeedItemsAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Feeds_Item_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Facade.php';

        if (!$this->_helper->connectionCheck()) {
            $items = Conjoon_Modules_Groupware_Feeds_Item_Facade
                     ::getInstance()->getFeedItemsForUser(
                        $this->_helper->registryAccess->getUserId()
                     );
            $this->view->success = true;
            $this->view->items   = $items;
            $this->view->error   = null;
            return;
        }

        $items = Conjoon_Modules_Groupware_Feeds_Item_Facade::getInstance()
                 ->syncAndGetFeedItemsForUser(
                    $this->_helper->registryAccess->getUserId(),
                    $this->_request->getParam('removeold', false),
                    $this->_request->getParam('timeout', 30000)
                );

        $this->view->success = true;
        $this->view->items   = $items;
        $this->view->error   = null;
    }

// -------- accounts

    /**
     * Adds another feed-account for the user.
     * This method will store the account-settings for the feed and immediately
     * store all items related to it. The items itself will be returned with the
     * view variable "items", the account will be available in the view-variable
     * "account".
     */
    public function addFeedAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Feeds_Account_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Account/Facade.php';

        $result = Conjoon_Modules_Groupware_Feeds_Account_Facade::getInstance()
        ->addAccountAndImport(array(
            'deleteInterval' => $this->_request->getParam('deleteInterval'),
            'name'           => $this->_request->getParam('name'),
            'requestTimeout' => $this->_request->getParam('requestTimeout'),
            'updateInterval' => $this->_request->getParam('updateInterval'),
            'uri'            => $this->_request->getParam('uri')
        ), $this->_helper->registryAccess()->getUserId());

       if (!empty($result)) {
            $this->view->success = true;
            $this->view->error   = null;
            $this->view->account = $result['account'];
            $this->view->items   = $result['items'];
        }
    }

    /**
     * Action for saving account configuratiom
     * 2 Arrays will be submitted, one named "deleted", holding all id's of the accounts that
     * should be removed from the store, and one named "updated", holding all objects
     * representing the accounts that should be updated.
     * Depending on the context, either json-encoded strings will be available, or plain
     * arrays.
     * This action will also request to delete any feed items that where cached, tagged with either
     * the ids of the deleted or updated accounts.
     */
    public function updateAccountsAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Feeds_Account_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Account/Facade.php';

        $deleted = $this->_request->getParam('deleted');
        $updated = $this->_request->getParam('updated');

        $toUpdateIds = array();
        for ($i = 0, $len = count($updated); $i < $len; $i++) {
            $toUpdateIds[] = $updated[$i]['id'];
        }

        $facade = Conjoon_Modules_Groupware_Feeds_Account_Facade::getInstance();

        $userId = $this->_helper->registryAccess()->getUserId();

        $actRemoved = $facade->removeAccountsForIds($deleted, $userId);
        $actUpdated = $facade->updateAccounts($updated, $userId);

        $removeFailed  = array_diff($deleted, $actRemoved);
        $updatedFailed = array_diff($toUpdateIds, $actUpdated);

        $this->view->success        = empty($removeFailed) && empty($updatedFailed)
                                      ? true : false;
        $this->view->updatedFailed = $updatedFailed;
        $this->view->deletedFailed = $removeFailed;
        $this->view->error         = null;

    }

    /**
     * Queries and assigns all feed accounts belonging to the currently logged in
     * user to the view
     */
    public function getFeedAccountsAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Feeds_Account_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Account/Facade.php';

        $data = Conjoon_Modules_Groupware_Feeds_Account_Facade::getInstance()
                ->getAccountsForUser($this->_helper->registryAccess()->getUserId());

        $this->view->success  = true;
        $this->view->accounts = $data;
        $this->view->error    = null;
    }


    /**
     * Checks wether the given uri points to a valid feed container.
     *
     */
    public function isFeedAddressValidAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Feeds_ImportHelper
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/ImportHelper.php';

        $success = Conjoon_Modules_Groupware_Feeds_ImportHelper
                   ::isFeedAddressValid($this->_request->getParam('uri'));

        $this->view->success = $success;
        $this->view->error   = null;
    }

    /**
     * Flags a specific feed item as either read or unread, based on the passed
     * arguments.
     * Expects two request params "read" and "unread", each holding an array with feed
     * item ids to either flag as "read" or "unread.
     * The method will never return an error itself, as the operation on the underlying
     * datastore will not affect interaction critically.
     */
    public function setItemReadAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Feeds_Item_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Facade.php';

        Conjoon_Modules_Groupware_Feeds_Item_Facade::getInstance()
        ->setItemsRead(
            $this->_request->getParam('read'),
            $this->_request->getParam('unread')
        );

        $this->view->success = true;
        $this->view->error   = null;
    }

    /**
     * Returns the feed item (dto) with it's content.
     *
     */
    public function getFeedContentAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Feeds_Item_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Facade.php';

        $item = Conjoon_Modules_Groupware_Feeds_Item_Facade::getInstance()
                ->getFeedContent(
                    $this->_request->getParam('id'),
                    $this->_request->getParam('groupwareFeedsAccountsId'),
                    $this->_helper->registryAccess()->getUserId()
                );

        if ($item == null) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            $this->view->success = false;
            $this->view->item    = null;
            $this->view->error   = Conjoon_Error_Factory::createError(
                "The requested feed item was not found on the server.",
                Conjoon_Error::LEVEL_ERROR,
                Conjoon_Error::DATA
            )->getDto();
        } else {
            $this->view->success = true;
            $this->view->item    = $item;
            $this->view->error   = null;
        }
    }
}