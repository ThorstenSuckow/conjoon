<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
 * @see Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';


class Groupware_FeedsAccountController extends Zend_Controller_Action {

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
                       ->addActionContext('get.feed.accounts',     self::CONTEXT_JSON)
                       ->addActionContext('add.feed',              self::CONTEXT_JSON)
                       ->addActionContext('update.accounts',       self::CONTEXT_JSON)
                       ->initContext();

        $this->_helper->filterRequestData()
                      ->registerFilter('Groupware_FeedsAccountController::update.accounts')
                      ->registerFilter('Groupware_FeedsAccountController::is.feed.address.valid')
                      ->registerFilter('Groupware_FeedsAccountController::add.feed');
    }

    /**
     * Adds another feed-account for the user.
     * This method will store the account-settings for the feed and immediately
     * store all items related to it. The items itself will be returned with the
     * view variable "items", the account will be available in the view-variable
     * "account".
     */
    public function addFeedAction()
    {
        /*@REMOVE@*/
        if (!$this->_helper->connectionCheck()) {

            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            $this->view->success    = false;
            $this->view->error      = Conjoon_Error_Factory::createError(
                "Unexpected connection failure while trying to add feed account. "
                ."Please try again.",
                Conjoon_Error::LEVEL_WARNING,
                Conjoon_Error::DATA
            )->getDto();

            return;
        }
        /*@REMOVE@*/

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
        /*@REMOVE@*/
        if (!$this->_helper->connectionCheck()) {

            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            $this->view->success    = false;
            $this->view->error      = null;

            return;
        }
        /*@REMOVE@*/

        /**
         * @see Conjoon_Modules_Groupware_Feeds_ImportHelper
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/ImportHelper.php';

        $success = Conjoon_Modules_Groupware_Feeds_ImportHelper
                   ::isFeedAddressValid($this->_request->getParam('uri'));

        $this->view->success = $success;
        $this->view->error   = null;
    }


}