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
 * @see Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';


class Groupware_FeedsItemController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('get.feed.items', self::CONTEXT_JSON)
                       ->addActionContext('set.item.read', self::CONTEXT_JSON)
                       ->addActionContext('get.feed.content', self::CONTEXT_JSON)
                       ->initContext();

        $this->_helper->filterRequestData()
                      ->registerFilter('Groupware_FeedsItemController::get.feed.items', true)
                      ->registerFilter('Groupware_FeedsItemController::set.item.read')
                      ->registerFilter('Groupware_FeedsItemController::get.feed.content');
    }

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

        /*@REMOVE@*/
        if (!$this->_helper->connectionCheck()) {
            $items = array();
            if ($this->_request->getParam('removeold')) {
                $items = Conjoon_Modules_Groupware_Feeds_Item_Facade
                         ::getInstance()->getFeedItemsForUser(
                            $this->_helper->registryAccess->getUserId()
                         );
            }
            $this->view->success = true;
            $this->view->items   = $items;
            $this->view->error   = null;
            return;
        }
        /*@REMOVE@*/

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