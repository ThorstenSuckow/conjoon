<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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



class Groupware_FeedsController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $contextSwitch = $this->_helper->contextSwitch();

        $contextSwitch->addActionContext('is.feed.address.valid', self::CONTEXT_JSON)
                      ->addActionContext('get.feed.items', self::CONTEXT_JSON)
                      ->addActionContext('get.feed.accounts', self::CONTEXT_JSON)
                      ->addActionContext('set.item.read', self::CONTEXT_JSON)
                      ->addActionContext('add.feed', self::CONTEXT_JSON)
                      ->addActionContext('update.accounts', self::CONTEXT_JSON)
                      ->addActionContext('get.feed.content', self::CONTEXT_JSON)
                      ->initContext();
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
        require_once 'Intrabuild/Keys.php';
        require_once 'Zend/Feed.php';

        require_once 'Intrabuild/BeanContext/Decorator.php';
        $model = new Intrabuild_BeanContext_Decorator(
            'Intrabuild_Modules_Groupware_Feeds_Account_Model_Account'
        );
        $itemModel = new Intrabuild_BeanContext_Decorator(
            'Intrabuild_Modules_Groupware_Feeds_Item_Model_Item'
        );

        $removeOld = $this->_request->getParam('removeold');

        $auth   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $time = time();

        $accounts = $model->getAccountsToUpdateAsDto($userId, $time);

        $updatedAccounts = array();
        $insertedItems   = array();
        for ($i = 0, $len = count($accounts); $i < $len; $i++) {
            try {
                $import = Zend_Feed::import($accounts[$i]->uri);
                $items = $this->_importFeedItems($import, $accounts[$i]->id);
                for ($a = 0, $lena = count($items); $a < $lena; $a++) {
                    $added = $itemModel->addItemIfNotExists($items[$a], $accounts[$i]->id);
                    if ($added != -1 && $removeOld != true) {
                        $it = $items[$i];
                        Intrabuild_Util_Array::camelizeKeys($it);
                        $object = Intrabuild_BeanContext_Inspector::create(
                            'Intrabuild_Modules_Groupware_Feeds_Item',
                            $it
                        );
                        $object->setName($accounts[$i]->name);
                        $object = $object->getDto();
                        $this->_transformItemDto($object);
                        $insertedItems[] = $object;
                    }
                    $updatedAccounts[$accounts[$i]->id] = true;
                }
            } catch (Exception $e) {
                // ignore all!
            }
        }

        // set the last updated timestamp for the accounts
        $model->setLastUpdated(array_keys($updatedAccounts), $time);

        if ($removeOld == true) {
            $model->deleteOldFeedItems($userId);
            $items = $this->_getFeedItems();
        } else {
            // send all items that where added during this request
            // to the client
            $items = $insertedItems;
        }



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
        require_once 'Zend/Feed.php';
        require_once 'Intrabuild/Util/Array.php';
        require_once 'Intrabuild/Keys.php';
        require_once 'Intrabuild/BeanContext/Inspector.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/Account/Model/Account.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/Item/Model/Item.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/Account/Filter/Account.php';

        $model  = new Intrabuild_Modules_Groupware_Feeds_Account_Model_Account();

        $auth   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $classToCreate = 'Intrabuild_Modules_Groupware_Feeds_Account';

        $this->view->success = true;
        $this->view->error = null;

        try {
            $filter = new Intrabuild_Modules_Groupware_Feeds_Account_Filter_Account(
                $_POST,
                Intrabuild_Filter_Input::CONTEXT_CREATE
            );
            $filteredData = $filter->getProcessedData();

            $import = Zend_Feed::import($filteredData['uri']);

            require_once 'Zend/Filter/HtmlEntities.php';
            $htmlEntities = new Zend_Filter_HtmlEntities(ENT_COMPAT, 'UTF-8');
            $filteredData['title'] = $htmlEntities->filter($import->title());
            $filteredData['link'] = $import->link();

            // atom feeds may have more than 1 link tag. Simply take the first one's
            // node value
            if (!is_string($filteredData['link'])) {
                $filteredData['link'] = $filteredData['link'][0]->firstChild->data;
                if (!is_string($filteredData['link'])) {
                    // fallback - use the uri
                    $filteredData['link'] = $filteredData['uri'];
                }
            }

            $filteredData['description'] = $import->description();
            $data = $filteredData;
            Intrabuild_Util_Array::underscoreKeys($data);
            $data['user_id'] = $userId;
            $data['last_updated'] = time();

            $insertId = $model->addAccount($data);
            if ($insertId <= 0) {
                $this->view->success = false;
                return;
            }
            $filteredData['id'] = $insertId;
            $this->view->account = Intrabuild_BeanContext_Inspector::create(
                $classToCreate,
                $filteredData
            )->getDto();

            $itemModel = new Intrabuild_Modules_Groupware_Feeds_Item_Model_Item();

            $data = $this->_importFeedItems($import, $filteredData['id']);

            for ($i = 0, $len = count($data); $i < $len; $i++) {
                $itemModel->insert($data[$i]);
            }

            $this->view->items = $this->_getFeedItems($filteredData['id']);

        } catch (Zend_Filter_Exception $e) {
            require_once 'Intrabuild/Error.php';
            $error = Intrabuild_Error::fromFilter($filter, $e);
            $accountData = $_POST;
            $this->view->account = Intrabuild_BeanContext_Inspector::create(
                $classToCreate,
                $_POST
            )->getDto();
            $this->view->success = false;
            $this->view->error = $error->getDto();
        }
    }

    /**
     * Action for saving account configuratiom
     * 2 Arrays will be submitted, one named "deleted", holding all id's of the accounts that
     * should be removed from the store, and one named "updated", holding all objects
     * representing the accounts that should be updated.
     * Depending on the context, either json-encoded strings will be available, or plain
     * arrays.
     */
    public function updateAccountsAction()
    {
        require_once 'Intrabuild/Modules/Groupware/Feeds/Account/Filter/Account.php';
        require_once 'Intrabuild/Util/Array.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/Account/Model/Account.php';

        $toDelete      = array();
        $toUpdate      = array();
        $deletedFailed = array();
        $updatedFailed = array();

        $model   = new Intrabuild_Modules_Groupware_Feeds_Account_Model_Account();

        $data  = array();
        $error = null;

        if ($this->_helper->contextSwitch()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toDelete = Zend_Json::decode($_POST['deleted'], Zend_Json::TYPE_ARRAY);
            $toUpdate = Zend_Json::decode($_POST['updated'], Zend_Json::TYPE_ARRAY);
        }

        for ($i = 0, $len = count($toDelete); $i < $len; $i++) {
            $affected = $model->deleteAccount($toDelete[$i]);
            if (!$affected) {
                $deletedFailed[] = $toDelete[$i];
            }
        }

        for ($i = 0, $len = count($toUpdate); $i < $len; $i++) {
            $_ = $toUpdate[$i];
            $filter = new Intrabuild_Modules_Groupware_Feeds_Account_Filter_Account(
                $_,
                Intrabuild_Filter_Input::CONTEXT_UPDATE
            );
            try {
                $data[$i] = $filter->getProcessedData();
                Intrabuild_Util_Array::underscoreKeys($data[$i]);
            } catch (Zend_Filter_Exception $e) {
                 require_once 'Intrabuild/Error.php';
                 $error = Intrabuild_Error::fromFilter($filter, $e);
                 $this->view->success = false;
                 $this->view->updatedFailed = array($_['id']);
                 $this->view->deletedFailed = $deletedFailed;
                 $this->view->error = $error->getDto();
                 break;
            }
        }

        if ($error === null) {
            for ($i = 0, $len = count($data); $i < $len; $i++) {
                $id = $data[$i]['id'];
                unset($data[$i]['id']);
                $affected = $model->updateAccount($id, $data[$i]);
                if (!$affected) {
                    $updatedFailed[] = $id;
                }
            }

            $this->view->success        = empty($updatedFailed) ? true : false;
            $this->view->updatedFailed = $updatedFailed;
            $this->view->deletedFailed = $deletedFailed;
            $this->view->error         = null;
        }
    }

    /**
     * Queries and assigns all feed accounts belonging to the currently logged in
     * user to the view
     */
    public function getFeedAccountsAction()
    {
        require_once 'Intrabuild/Keys.php';
        $user = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();

        require_once 'Intrabuild/BeanContext/Decorator.php';
        $decoratedModel = new Intrabuild_BeanContext_Decorator(
            'Intrabuild_Modules_Groupware_Feeds_Account_Model_Account'
        );

        $data = $decoratedModel->getAccountsForUserAsDto($user->getId());

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
        require_once 'Zend/Feed.php';

        $uri = $_POST['uri'];

        $feed = null;
        $this->view->success = true;
        $this->view->error   = null;
        try {
            $feed = Zend_Feed::import($uri);
        } catch (Zend_Feed_Exception $e) {
            $this->view->success = false;
        }
    }

    /**
     * Flags a specific feed item as either read or unread, based on the passed
     * arguments.
     * Data will be comin via post, whereas in json context a json-encoded
     * string will be submitted, which can be found in the $_POST var keyed
     * with "json".
     * The method will never return an error itself, as the operation on teh udnerlying
     * datastore will not affect Uinteraction critically.
     */
    public function setItemReadAction()
    {
        if ($this->_helper->contextSwitch()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toUpdate = Zend_Json::decode($_POST['json'], Zend_Json::TYPE_ARRAY);
        }

        require_once 'Intrabuild/Modules/Groupware/Feeds/Item/Filter/Item.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/Item/Model/Item.php';
        require_once 'Intrabuild/Util/Array.php';

        $model = new Intrabuild_Modules_Groupware_Feeds_Item_Model_Item();

        $filter = new Intrabuild_Modules_Groupware_Feeds_Item_Filter_Item(
            array(),
            Intrabuild_Modules_Groupware_Feeds_Item_Filter_Item::CONTEXT_READ
        );

        $read   = array();
        $unread = array();
        for ($i = 0, $len = count($toUpdate); $i < $len; $i ++) {
            $filter->setData($toUpdate[$i]);
            $data = $filter->getProcessedData();
            if ($data['isRead']) {
                $read[] = $data['id'];
            } else {
                $unread[] = $data['id'];
            }
        }

        $model->setItemRead($read,   true);
        $model->setItemRead($unread, false);

        $this->view->success = true;
        $this->view->error   = null;

    }

    /**
     * Returns the feed item (dto) with it's content.
     *
     */
    public function getFeedContentAction()
    {
        require_once 'Intrabuild/BeanContext/Decorator.php';
        $itemModel = new Intrabuild_BeanContext_Decorator(
            'Intrabuild_Modules_Groupware_Feeds_Item_Model_Item'
        );

        $id = $this->_request->getParam('id', 0);

        $item = $itemModel->getItemAsDto($id);

        if ($item == null) {
            $this->view->success = true;
            $this->view->item    = null;
            $this->view->error   = null;
        } else {
            $this->view->success = true;
            $this->view->item    = $item;
            $this->view->error   = null;
        }

    }



// -------- helper

    /**
     * Imports all feed items from a given cross domain source.
     */
    private function _importFeedItems($import, $accountId)
    {
        require_once 'Zend/Date.php';
        require_once 'Intrabuild/Util/Array.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/Item/Filter/Item.php';

        $dateInputFormat = Zend_Date::TIMESTAMP;

        switch (get_class($import)) {
            case 'Zend_Feed_Atom':
                $dateInputFormat = Zend_Date::ATOM;
            break;

            case'Zend_Feed_Rss':
                $dateInputFormat = Zend_Date::RSS;
            break;
        }

        $data = array();

        foreach ($import as $item) {

            $itemData = array();
            $itemData['groupwareFeedsAccountsId'] = $accountId;

            $itemData['title'] = $item->title();

            // author
            if ($author = $item->author()) {
                $itemData['author'] = $author;
            } else if ($author = $item->creator()) {
                $itemData['author'] = $author;
            }

             // description
            if ($description = $item->description()) {
                $itemData['description'] = $description;
            } else if ($description = $item->summary()) {
                $itemData['description'] = $itemData['title'];
            }

            // content
            if ($content = $item->content()) {
                $itemData['content'] = $content;
            } else if ($itemData['description']) {
                $itemData['content'] = $itemData['description'];
            } else {
                $itemData['content'] = $itemData['description'];
            }

            // link
            if ($link = $item->link()) {
                $itemData['link'] = $link;
            } else if ($link = $item->link['href']) {
                $itemData['link'] = $link;
            } else if ($link = $item->link('alternate')) {
                $itemData['link'] = $link;
            }

            // guid
            if ($link = $item->id()) {
                $itemData['guid'] = $link;
            } else if ($link = $item->guid()) {
                $itemData['guid'] = $link;
            } else {
                $itemData['guid'] = $itemData['link'];
            }

            // pubDate
            if ($pubDate = $item->updated()) {
                $date = new Zend_Date($pubDate, $dateInputFormat);
            } else if ($pubDate = $item->pubDate()) {
                $date = new Zend_Date($pubDate, $dateInputFormat);
            } else {
                $date = new Zend_Date();
            }
            $itemData['pubDate'] = $date->get(Zend_Date::ISO_8601);
            $itemData['savedTimestamp'] = time();
            $filter = new Intrabuild_Modules_Groupware_Feeds_Item_Filter_Item(
                $itemData,
                Intrabuild_Filter_Input::CONTEXT_CREATE
            );
            $fillIn = $filter->getProcessedData();
            Intrabuild_Util_Array::underscoreKeys($fillIn);
            $data[] = $fillIn;
        }

        return $data;
    }

    /**
     * Read out all feeds without the field 'content'
     */
    private function _getFeedItems($accountId = null)
    {
        require_once 'Intrabuild/Keys.php';
        require_once 'Intrabuild/BeanContext/Decorator.php';
        $model = new Intrabuild_BeanContext_Decorator(
            'Intrabuild_Modules_Groupware_Feeds_Account_Model_Account'
        );
        $itemModel = new Intrabuild_BeanContext_Decorator(
            'Intrabuild_Modules_Groupware_Feeds_Item_Model_Item'
        );

        $user = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        if ($accountId === null) {
            $data = $model->getAccountsForUserAsDto($user->getId());
        } else {
            $data = array($model->getAccountAsDto($accountId));
        }

        $accounts = array();
        $items    = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $tmpItems = $itemModel->getItemsForAccountAsDto($data[$i]->id);
            for ($a = 0, $len2 = count($tmpItems); $a < $len2; $a++) {
                $items[] = $tmpItems[$a];
                $this->_transformItemDto($items[$a]);
            }
        }

        return $items;
    }

    /**
     * Helper for stripping not needed information from an instance of
     * Intrabuild_Modules_Groupware_Feeds_ItemDto for sending it to the client.
     *
     */
    private function _transformItemDto(Intrabuild_Modules_Groupware_Feeds_Item_Dto $item)
    {
        $item->content = null;
        unset($item->guid);
        $item->description = $item->description ? substr($item->description, 0, 128).'...' : '';
    }

}
?>