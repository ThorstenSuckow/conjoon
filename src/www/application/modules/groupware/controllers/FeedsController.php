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
     * Returns all feed accounts out of the database belonging to the current user.
     */
    public function getFeedAccountsAction()    
    {    
        require_once 'Intrabuild/Modules/Groupware/Feeds/AccountModel.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/ItemModel.php';
        
        $model     = new Intrabuild_Modules_Groupware_Feeds_AccountModel();
        $itemModel = new Intrabuild_Modules_Groupware_Feeds_ItemModel();
        
        $user = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        $data = $model->getAccountsForUser($user->getId());
        
        $accounts = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $accounts[] = $data[$i]->getDto();
        }
        
        $this->view->success  = true;
        $this->view->accounts = $accounts;
        $this->view->error    = null;
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
        require_once 'Intrabuild/Keys.php';
        require_once 'Intrabuild/BeanContext/Inspector.php';
        require_once 'Intrabuild/Util/Array.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/AccountModel.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/ItemModel.php';
        require_once 'Zend/Feed.php';
 
        $removeOld = $this->_request->getParam('removeold');
 
        $model      = new Intrabuild_Modules_Groupware_Feeds_AccountModel();
        $itemModel  = new Intrabuild_Modules_Groupware_Feeds_ItemModel();
        
        $auth   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();
        
        $accounts = $model->getAccountsToUpdate($userId, true); 
        
        $updatedAccounts = array();
        $insertedItems   = array();
        for ($i = 0, $len = count($accounts); $i < $len; $i++) {
            try {
                $import = Zend_Feed::import($accounts[$i]['uri']);
                $items = $this->_importFeedItems($import, $accounts[$i]['id']);
                for ($a = 0, $lena = count($items); $a < $lena; $a++) {
                    $added = $itemModel->addItemIfNotExists($items[$a], $accounts[$i]['id']);            
                    if ($added != -1 && $removeOld != true) {
                        $object = Intrabuild_BeanContext_Inspector::create(
                            'Intrabuild_Modules_Groupware_Feeds_Item',
                            Intrabuild_Util_Array::camelizeKeys($items[$i])
                        );
                        $object->setName($accounts[$i]['name']);
                        $object = $object->getDto();
                        $this->_transformItemDto($object);
                        $insertedItems[] = $object;
                    }
                    $updatedAccounts[$accounts[$i]['id']] = true;
                }
            } catch (Exception $e) {
                // ignore all!    
            }
        } 
        
        $where = $model->getAdapter()->quoteInto('id IN (?)', implode(',', array_keys($updatedAccounts)));    
        $model->update(
            array('last_updated' => time()),
            $where
        );    
            
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
        require_once 'Intrabuild/Modules/Groupware/Feeds/AccountModel.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/ItemModel.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/AccountFilter.php';
        
        
        $model  = new Intrabuild_Modules_Groupware_Feeds_AccountModel();
        
        $auth   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();
        
        $classToCreate = 'Intrabuild_Modules_Groupware_Feeds_Account';
        
        $this->view->success = true;
        $this->view->error = null;            
        
        try {
            $filter = new Intrabuild_Modules_Groupware_Feeds_AccountFilter($_POST, Intrabuild_Filter_Input::CONTEXT_CREATE);
            $filteredData = $filter->getProcessedData();
            
            $import = Zend_Feed::import($filteredData['uri']);
            
            require_once 'Zend/Filter/HtmlEntities.php';
            $htmlEntities = new Zend_Filter_HtmlEntities();
            $filteredData['title'] = $htmlEntities->filter($import->title());
            $filteredData['link'] = $import->link();
            $filteredData['description'] = $import->description();
            $data = Intrabuild_Util_Array::underscoreKeys($filteredData);
            $data['user_id'] = $userId;
            $data['last_updated'] = time();
            $filteredData['id'] = $model->insert($data);
            $this->view->account = Intrabuild_BeanContext_Inspector::create($classToCreate, $filteredData)->getDto();
            
            $itemModel = new Intrabuild_Modules_Groupware_Feeds_ItemModel();
            
            $data = $this->_importFeedItems($import, $filteredData['id']);
            
            for ($i = 0, $len = count($data); $i < $len; $i++) {
                $itemModel->insert($data[$i]);    
            }
            
            $this->view->items = $this->_getFeedItems($filteredData['id']);
            
        } catch (Zend_Filter_Exception $e) {
            require_once 'Intrabuild/Error.php';
            $error = Intrabuild_Error::fromFilter($filter, $e);
            $accountData = $_POST;
            $this->view->account = Intrabuild_BeanContext_Inspector::create($classToCreate, $_POST)->getDto();
            $this->view->success = false;
            $this->view->error = $error->getDto();
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
        
        require_once 'Intrabuild/Modules/Groupware/Feeds/ItemFilter.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/ItemModel.php';
        require_once 'Intrabuild/Util/Array.php';
        
        $model = new Intrabuild_Modules_Groupware_Feeds_ItemModel();
        $adapter = $model->getAdapter();
        
        $filter = new Intrabuild_Modules_Groupware_Feeds_ItemFilter(array(), Intrabuild_Modules_Groupware_Feeds_ItemFilter::CONTEXT_READ);
        
        for ($i = 0, $len = count($toUpdate); $i < $len; $i ++) {
            $filter->setData($toUpdate[$i]);
            $data = $filter->getProcessedData();    
            
            $data  = Intrabuild_Util_Array::underscoreKeys($data);
            $where = $adapter->quoteInto('id = ?', $data['id'], 'INTEGER');
            $model->update($data, $where);
        }
        
        $this->view->success = true;
        $this->view->error   = null;
        
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
        require_once 'Intrabuild/Modules/Groupware/Feeds/AccountFilter.php';
        require_once 'Intrabuild/Util/Array.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/AccountModel.php';
        
        $toDelete      = array();
        $toUpdate      = array();
        $deletedFailed = array();
        $updatedFailed = array();
        
        $model   = new Intrabuild_Modules_Groupware_Feeds_AccountModel();
        $adapter = $model->getAdapter();
        
        $data  = array();
        $error = null;
        
        if ($this->_helper->contextSwitch()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toDelete = Zend_Json::decode($_POST['deleted'], Zend_Json::TYPE_ARRAY);
            $toUpdate = Zend_Json::decode($_POST['updated'], Zend_Json::TYPE_ARRAY);
        }
        
        for ($i = 0, $len = count($toDelete); $i < $len; $i++) {
            $affected = $model->deleteAccount($toDelete[$i]);
            if ($affected == 0) {
                $deletedFailed[] = $toDelete[$i];    
            }
        }
        
        for ($i = 0, $len = count($toUpdate); $i < $len; $i++) {
            $_ = $toUpdate[$i];
            $filter = new Intrabuild_Modules_Groupware_Feeds_AccountFilter($_, Intrabuild_Filter_Input::CONTEXT_UPDATE);
            try {
                $data[$i] = Intrabuild_Util_Array::underscoreKeys($filter->getProcessedData());
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
                $where    = $adapter->quoteInto('id = ?', $data[$i]['id'], 'INTEGER');
                $affected = $model->update($data[$i], $where);
                if ($affected == 0) {
                    $updatedFailed[] = $data[$i]['id'];
                }
            }
            
            $this->view->success        = empty($updatedFailed) ? true : false;
            $this->view->updatedFailed = $updatedFailed;
            $this->view->deletedFailed = $deletedFailed;
            $this->view->error         = null;
        }             
    }
    
    /**
    * Returns the feed item (dto) with it's content. 
    *
    */
    public function getFeedContentAction()
    {
        require_once 'Intrabuild/Modules/Groupware/Feeds/ItemModel.php';
        
        $model   = new Intrabuild_Modules_Groupware_Feeds_ItemModel();
        
        $id = (int)$_POST['id'];
        $item = $model->getItem($id);
        
        if ($item == null) {
            $this->view->success = true;
            $this->view->item = null;
            $this->view->error = null;
        } else {
            $this->view->success = true;
            $this->view->item = $item->getDto();
            $this->view->error = null;
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
        require_once 'Intrabuild/Modules/Groupware/Feeds/ItemFilter.php';
        
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
            $filter = new Intrabuild_Modules_Groupware_Feeds_ItemFilter($itemData, Intrabuild_Filter_Input::CONTEXT_CREATE);    
            $data[] = Intrabuild_Util_Array::underscoreKeys($filter->getProcessedData()); 
        } 
        
        return $data;
    } 

    /**
     * Read out all feeds without the field 'content'
     */
    private function _getFeedItems($accountId = null)
    {
        require_once 'Intrabuild/Modules/Groupware/Feeds/AccountModel.php';
        require_once 'Intrabuild/Modules/Groupware/Feeds/ItemModel.php';
        
        $model     = new Intrabuild_Modules_Groupware_Feeds_AccountModel();
        $itemModel = new Intrabuild_Modules_Groupware_Feeds_ItemModel();
        
        $user = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        if ($accountId === null) {
            $data = $model->getAccountsForUser($user->getId(), true);
        } else {
            $data = array($model->getAccount($accountId, true));
        }
        
        $accounts = array();
        $items = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $tmpItems = $itemModel->getItemsForAccount($data[$i]['id']); 
            for ($a = 0, $len2 = count($tmpItems); $a < $len2; $a++) {
                $nItem = $tmpItems[$a]->getDto();
                $this->_transformItemDto($nItem);
                $items[] = $nItem;
            }
        }
        
        return $items;
    }
    
    /**
     * Helper for stripping not needed information from an instance of 
     * Intrabuild_Modules_Groupware_Feeds_ItemDto for sending it to the client.
     * 
     */
    private function _transformItemDto(Intrabuild_Modules_Groupware_Feeds_ItemDto $item)
    {
        $item->content = null;
        unset($item->guid);
        $nItem->description = $nItem->description ? substr($nItem->description, 0, 128).'...' : '';
    } 
     
}
?>