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
 * Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

/**
 * Action controller for the email module.
 * This controller provides context-switch functionality to deliver
 * data in different formats to the client.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Groupware_EmailController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $contextSwitch = $this->_helper->contextSwitch();

        $contextSwitch
                      // account actions
                      ->addActionContext('add.email.account', self::CONTEXT_JSON)
                      ->addActionContext('get.email.accounts', self::CONTEXT_JSON)
                      ->addActionContext('update.email.accounts', self::CONTEXT_JSON)
                      // item actions
                      ->addActionContext('fetch.emails', self::CONTEXT_JSON)
                      ->addActionContext('move.items', self::CONTEXT_JSON)
                      ->addActionContext('delete.items', self::CONTEXT_JSON)
                      ->addActionContext('get.email.items', self::CONTEXT_JSON)
                      ->addActionContext('get.email', self::CONTEXT_JSON)
                      ->addActionContext('set.email.flag', self::CONTEXT_JSON)
                      // folder actions
                      ->addActionContext('get.folder', self::CONTEXT_JSON)
                      ->addActionContext('rename.folder', self::CONTEXT_JSON)
                      ->addActionContext('add.folder', self::CONTEXT_JSON)
                      ->addActionContext('move.folder', self::CONTEXT_JSON)
                      ->addActionContext('delete.folder', self::CONTEXT_JSON)
                      // editing emails
                      ->addActionContext('get.draft', self::CONTEXT_JSON)
                      ->initContext();
    }

// -------- fetching emails
    /**
     * Queriesan email account for new wmails. If an account id was posted, only the
     * specified account will be queried.
     * Sends all newly fetched emails back to the client.
     *
     */
    public function fetchEmailsAction()
    {
        if (isset($_POST['accountId'])) {
            $accountId = (int)$_POST['accountId'];
            if ($accountId <= 0) {
                $this->view->success    = true;
                $this->view->totalCount = 0;
                $this->view->items      = array();
                $this->view->error      = null;
                return;
            }
        } else {
            $accountId = null;
        }

        require_once 'Intrabuild/Keys.php';
        require_once 'Intrabuild/Modules/Groupware/Email/Letterman.php';
        require_once 'Intrabuild/BeanContext/Decorator.php';
        require_once 'Intrabuild/Error.php';

        $auth   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $emails        = array();
        $errorMessages = array();

        $time = time();
        $currentAccount = null;
        try {
            $accountDecorator = new Intrabuild_BeanContext_Decorator(
                'Intrabuild_Modules_Groupware_Email_Account_Model_Account'
            );

            $noop = false;
            if ($accountId === null) {
                $accounts = $accountDecorator->getAccountsForUserAsEntity($userId);
                if (count($accounts) == 0) {
                    $noop = true;
                }
            } else {
                $account = $accountDecorator->getAccountAsEntity($accountId, $userId);
                if ($account == null) {
                    $noop = true;
                } else {
                    $accounts = array($account);
                }
            }

            if ($noop) {
                $this->view->success    = true;
                $this->view->totalCount = 0;
                $this->view->items      = array();
                $this->view->error      = null;
                return;
            }

            for ($i = 0, $accLen = count($accounts); $i < $accLen; $i++) {
                $accId = $accounts[$i]->getId();
                if ($accId <= 0) {
                    continue;
                }
                $currentAccount =& $accounts[$i];
                $tmpEmails = Intrabuild_Modules_Groupware_Email_Letterman::fetchEmails($userId, $accId);

                $emails        = array_merge($emails, $tmpEmails['fetched']);
                $errorMessages = array_merge($errorMessages, $tmpEmails['errors']);
            }


        } catch (Zend_Mail_Protocol_Exception $e) {
            $error = Intrabuild_Error::fromException($e)->getDto();
            $error->title = 'Error while connecting to host';
            $error->message = $e->getMessage()."<br /> - host: ".
                              $currentAccount->getServerInbox().':'.
                              $currentAccount->getPortInbox().'<br /> user: '.
                              $currentAccount->getUsernameInbox().' (using password: '.
                              (strlen($currentAccount->getPasswordInbox()) > 0 ? 'yes' : 'no').')';
            $error->level = Intrabuild_Error::LEVEL_ERROR;
            $this->view->error = $error;
            $this->view->success    = false;
            $this->view->totalCount = 0;
            $this->view->items      = array();
            return;
        }

        $len = count($emails);
        if ($len > 0) {
            require_once 'Intrabuild/BeanContext/Decorator.php';
            require_once 'Intrabuild/Modules/Groupware/Email/Item/Filter/ItemResponse.php';

            $decoratedModel = new Intrabuild_BeanContext_Decorator(
                'Intrabuild_Modules_Groupware_Email_Item_Model_Inbox',
                new Intrabuild_Modules_Groupware_Email_Item_Filter_ItemResponse(
                    array(),
                    Intrabuild_Filter_Input::CONTEXT_RESPONSE
                )
            );

            $emails = $decoratedModel->getLatestEmailItemsForAsDto(
                $userId,
                $time,
                array(
                    'start' => 0,
                    'limit' => $len,
                    'dir'   => 'ASC',
                    'sort'  => 'id'
                )
            );
        }

        $this->view->success    = true;
        $this->view->totalCount = $len;
        $this->view->items      = $emails;
        $this->view->error      = null;

        if (count($errorMessages) > 0) {
            $error = new Intrabuild_Error();
            $error = $error->getDto();;
            $error->title = 'Error while fetching email(s)';
            $error->message = implode('<br />', $errorMessages);
            $error->level = Intrabuild_Error::LEVEL_ERROR;
            $this->view->error = $error;
        }


    }

// -------- email folder

    /**
     * Deletes a folder permanently
     *
     * POST:
     * - integer id : the id of the folder to delete
     */
    public function deleteFolderAction()
    {
        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Intrabuild_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Intrabuild_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_DELETE
        );

        $filteredData = array();
        try {
            $filteredData = $filter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            require_once 'Intrabuild/Error.php';
            $error = Intrabuild_Error::fromFilter($filter, $e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Intrabuild_Modules_Groupware_Email_Folder_Model_Folder();

        require_once 'Intrabuild/Keys.php';
        $user   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        $userId = $user->getId();

        $ret = $folderModel->deleteFolder($filteredData['id'], $userId);

        if ($ret === 0) {
            require_once 'Intrabuild/Error.php';
            $error = new Intrabuild_Error();
            $error = $error->getDto();
            $error->title   = 'Error';
            $error->level   = Intrabuild_Error::LEVEL_WARNING;
            $error->message = 'Could not delete the specified folder.';
            $this->view->success = false;
            $this->view->error   = $error;
            return;
        }

        $this->view->success = true;
        $this->view->error   = null;
    }

    /**
     * Moves a folder into a new folder.
     * POST:
     *  parentId : the id of the new folder this folder gets moved into
     *  id : the id of this folder thats about being moved
     *
     */
    public function moveFolderAction()
    {
        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Intrabuild_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Intrabuild_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_MOVE
        );

        $filteredData = array();
        try {
            $filteredData = $filter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            require_once 'Intrabuild/Error.php';
            $error = Intrabuild_Error::fromFilter($filter, $e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Intrabuild_Modules_Groupware_Email_Folder_Model_Folder();

        $ret = $folderModel->moveFolder($filteredData['id'], $filteredData['parentId']);

        if ($ret === 0) {
            require_once 'Intrabuild/Error.php';
            $error = new Intrabuild_Error();
            $error = $error->getDto();
            $error->title   = 'Error';
            $error->level   = Intrabuild_Error::LEVEL_WARNING;
            $error->message = 'Could not move the specified folder into the new folder.';
            $this->view->success = false;
            $this->view->error   = $error;
            return;
        }

        $this->view->success = true;
        $this->view->error   = null;
    }

    /**
     * Adds a new folder to the folder with the specified id.
     * POST:
     * parentId : the id of the parent folder to which the new folder should get
     * appended
     * name : the name of the folder
     *
     * The method will assign a view-id property called "id", which holds the
     * id of the newly added folder.
     */
    public function addFolderAction()
    {
        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Intrabuild_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Intrabuild_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_CREATE
        );

        $filteredData = array();
        try {
            $filteredData = $filter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            require_once 'Intrabuild/Error.php';
            $error = Intrabuild_Error::fromFilter($filter, $e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Intrabuild_Modules_Groupware_Email_Folder_Model_Folder();

        require_once 'Intrabuild/Keys.php';
        $user   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        $userId = $user->getId();

        $id = $folderModel->addFolder($filteredData['parentId'], $filteredData['name'], $userId);

        if ((int)$id <= 0) {
            $this->view->success = false;
            require_once 'Intrabuild/Error.php';
            $error = new Intrabuild_Error();
            $error = $error->getDto();
            $error->file  = __FILE__;
            $error->line  = __LINE__;
            $error->type  = Intrabuild_Error::UNKNOWN;
            $error->level = Intrabuild_Error::LEVEL_WARNING;
            $error->message = "Could not create folder.";
            $this->view->error = $error;
            return;
        }

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->id      = $id;

    }

    /**
     * Renames the folder with the specified it to the specified value.
     * Post vars:
     * id       : the id of the folder that gets renamed
     * name     : the new name of the node
     */
    public function renameFolderAction()
    {
        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Intrabuild_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Intrabuild_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_RENAME
        );

        $filteredData = array();
        try {
            $filteredData = $filter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            require_once 'Intrabuild/Error.php';
            $error = Intrabuild_Error::fromFilter($filter, $e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Intrabuild_Modules_Groupware_Email_Folder_Model_Folder();

        $ret = $folderModel->renameFolder($filteredData['id'], $filteredData['name']);

        if ($ret === 0) {
            require_once 'Intrabuild/Error.php';
            $error = new Intrabuild_Error();
            $error = $error->getDto();
            $error->title   = 'Error';
            $error->level   = Intrabuild_Error::LEVEL_WARNING;
            $error->message = 'Could not rename the specified folder.';
            $this->view->success = false;
            $this->view->error   = $error;
            return;
        }

        $this->view->success = true;
        $this->view->error   = null;
    }

    /**
     * Returns the email folder for a user. A user has always at least the
     * following nodes available in his mail application for storing emails:
     *
     * rootNode    => id  : root   | 0 custom children
     *     Inbox   => id  : inbox  | 0..n custom children
     *     Spam    => id  : spam   | 0..n custom children
     *     Outbox  => id  : outbox | 0 custom children
     *     Sent    => id  : sent   | 0..n custom children
     *     Trash   => id  : trash  | 0..n custom children
     *
     *
     * Each folder except the rootNode and the Outbox can have any amount of
     * children appended, which itself may be nested to any level.
     *
     * The method returns the folders which are direct childnodes, along with
     * the number of emails which remain unread in this folder yet. This attribute
     * is called "pending" since it also denotes remaining emails in the outbox
     * folder which have not been send yet.
     *
     * The param of the id of the node, which children to return, is stored in
     * the supplied Post parameter. The initial request will have the id 'root'
     * or "0".
     * It is up to the application to read out the root's property id mapped to
     * the logged in user out of the database and return the associated childs
     * accordingly.
     *
     * NOTE:
     * Folder names' will be delivered unescaped to the client, so the client has to take care
     * of appropriate html-encoding the given names.
     *
     */
    public function getFolderAction()
    {
        $parentId = trim(strtolower($_POST['node']));

        if ($parentId !== 'root') {
            $parentId = (int)$parentId;
        } else {
            $parentId = 0;
        }

        require_once 'Intrabuild/BeanContext/Decorator.php';
        $decoratedFolderModel = new Intrabuild_BeanContext_Decorator(
            'Intrabuild_Modules_Groupware_Email_Folder_Model_Folder'
        );

        require_once 'Intrabuild/Keys.php';
        $user   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        $userId = $user->getId();

        $rows = $decoratedFolderModel->getFoldersAsDto($parentId, $userId);

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->items   = $rows;
    }


// -------- email items

    /**
     * Deletes all items specified with the ids.
     *
     * Based on the given context, Post data will be in different format.
     * In Json context, there will be a post value named "json", holding the
     * item's id as a json-encoded associative array: { id : integer }
     *
     */
    public function deleteItemsAction()
    {
        if ($this->_helper->contextSwitch()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toDelete = Zend_Json::decode($_POST['json'], Zend_Json::TYPE_ARRAY);
        }

        require_once 'Intrabuild/Modules/Groupware/Email/Item/Filter/Item.php';

        $filter = new Intrabuild_Modules_Groupware_Email_Item_Filter_Item(
            array(),
            Intrabuild_Modules_Groupware_Email_Item_Filter_Item::CONTEXT_DELETE
        );

        $itemIds = array();
        for ($i = 0, $len = count($toDelete); $i < $len; $i++) {
            $filteredData = $filter->setData($toDelete[$i])->getProcessedData();
            $itemIds[] = $filteredData['id'];
        }

        require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Item.php';
        require_once 'Intrabuild/Keys.php';
        $auth   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $model = new Intrabuild_Modules_Groupware_Email_Item_Model_Item();

        $model->deleteItemsForUser($itemIds, $userId);

        $this->view->success = true;
        $this->view->error   = null;
    }

    /**
     * Moves all items to the folder with the specified id.
     *
     * Based on the given context, Post data will be in different format.
     * In Json context, there will be a post value named "json", holding the
     * item's id with their target folder as an associative array:
     * {id : integer, groupwareEmailFoldersId : integer }
     *
     *
     *
     */
    public function moveItemsAction()
    {
        if ($this->_helper->contextSwitch()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toMove = Zend_Json::decode($_POST['json'], Zend_Json::TYPE_ARRAY);
        }

        require_once 'Intrabuild/Modules/Groupware/Email/Item/Filter/Item.php';

        $filter = new Intrabuild_Modules_Groupware_Email_Item_Filter_Item(
            array(),
            Intrabuild_Modules_Groupware_Email_Item_Filter_Item::CONTEXT_MOVE
        );

        $moveData = array();
        for ($i = 0, $len = count($toMove); $i < $len; $i++) {
            $filteredData = $filter->setData($toMove[$i])->getProcessedData();
            if (!isset($moveData[$filteredData['groupwareEmailFoldersId']])) {
                $moveData[$filteredData['groupwareEmailFoldersId']] = array();
            }
            $moveData[$filteredData['groupwareEmailFoldersId']][] = $filteredData['id'];
        }

        require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Item.php';
        $model = new Intrabuild_Modules_Groupware_Email_Item_Model_Item();

        foreach ($moveData as $folderId => $itemIds) {
            $model->moveItemsToFolder($itemIds, $folderId);
        }

        $this->view->success = true;
        $this->view->error   = null;
    }

    /**
     * Returns the email items based on the passed POST-parameters to the client.
     * Possible POST parameters are:
     *
     * start - the index in the datastore from where to read the first record
     * limit - the number of records to return
     * dir   - the sort direction, either ASC or DESC
     * sort  - the field to sort. Fields are based upon the properties of the
     *         Intrabuild_Modules_Groupware_Email_ItemDto-class and have to be substituted
     *         to their appropriate representatives in the underlying datamodel.
     * groupwareEmailFoldersId - the id of the folder, for which the items should be loaded.
     *                           if this parameter is missing, all emails from all accounts
     *                           will be fetched
     * minDate - this argument is optional - if passed, all emails with a fetched_timestamp
     *           equal to or greater than minDate will be fetched
     *
     *
     * The assigned view variables are:
     *
     * items - an array with objects of Intrabuild_Modules_Groupware_Email_ItemDto
     * totalCount - the total count of records available for the requested folder
     *              for this user, or the total count of latest emails for the
     *              user since minDate
     * version - the version property for Ext.ux.grid.BufferedStore
     * pendingItems - if a folder id other than 0 was supplied, the number of
     * pending items will be read out and assigned to this view-variable
     *
     */
    public function getEmailItemsAction()
    {
        require_once 'Intrabuild/Modules/Groupware/Email/Item/Filter/Request.php';

        require_once 'Intrabuild/Util/Array.php';
        require_once 'Intrabuild/Keys.php';

        $auth   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $CONTEXT_REQUEST_LATEST = Intrabuild_Modules_Groupware_Email_Item_Filter_Request::CONTEXT_REQUEST_LATEST;
        $CONTEXT_REQUEST        = Intrabuild_Modules_Groupware_Email_Item_Filter_Request::CONTEXT_REQUEST;

        if (isset($_POST['minDate']) && !isset($_POST['groupwareEmailFoldersId'])) {
            $context = $CONTEXT_REQUEST_LATEST;
            $itemRequestFilter = new Intrabuild_Modules_Groupware_Email_Item_Filter_Request(
                $_POST,
                $context
            );
        } else {
            $context = $CONTEXT_REQUEST;
            $itemRequestFilter = new Intrabuild_Modules_Groupware_Email_Item_Filter_Request(
                $_POST,
                $context
            );
        }

        try {
            $filteredData = $itemRequestFilter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            $this->view->success    = true;
            $this->view->error      = null;
            $this->view->items      = array();
            $this->view->version    = 1;
            $this->view->totalCount = 0;
            return;
        }

        $sortInfo = array(
            'sort'  => $filteredData['sort'],
            'dir'   => $filteredData['dir'],
            'limit' => $filteredData['limit'],
            'start' => $filteredData['start']
        );



        require_once 'Intrabuild/BeanContext/Decorator.php';
        require_once 'Intrabuild/Modules/Groupware/Email/Item/Filter/ItemResponse.php';
        $itemResponseFilter = new Intrabuild_Modules_Groupware_Email_Item_Filter_ItemResponse(
            array(),
            Intrabuild_Filter_Input::CONTEXT_RESPONSE
        );

        $pendingItems = -1;

        if ($context == $CONTEXT_REQUEST) {

            // get the number of emails currently available for this folder
            // and this user
            require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Item.php';
            require_once 'Intrabuild/Modules/Groupware/Email/Folder/Model/Folder.php';

            $folderModel = new Intrabuild_Modules_Groupware_Email_Folder_Model_Folder();
            $itemModel   = new Intrabuild_Modules_Groupware_Email_Item_Model_Item();
            $totalCount  = $itemModel->getTotalItemCount($filteredData['groupwareEmailFoldersId']);

            if ($totalCount == 0) {
                $this->view->success      = true;
                $this->view->error        = null;
                $this->view->items        = array();
                $this->view->version      = 1;
                $this->view->totalCount   = 0;
                $this->view->pendingItems = 0;
                return;
            }

            $decoratedModel = new Intrabuild_BeanContext_Decorator(
                $itemModel,
                $itemResponseFilter
            );

            $folderDecorator = new Intrabuild_BeanContext_Decorator(
                $folderModel
            );


            $rows = $decoratedModel->getEmailItemsForAsDto(
                $userId,
                $filteredData['groupwareEmailFoldersId'],
                $sortInfo
            );

            $row = $folderDecorator->getFolderAsDto(
                $filteredData['groupwareEmailFoldersId'],
                $userId
            );

            $pendingItems = $row->pendingCount;

        } else if ($context == $CONTEXT_REQUEST_LATEST) {

            require_once 'Intrabuild/BeanContext/Decorator.php';
            require_once 'Intrabuild/Modules/Groupware/Email/Item/Filter/ItemResponse.php';
            require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Inbox.php';

            $itemInboxModel = new Intrabuild_Modules_Groupware_Email_Item_Model_Inbox();
            $totalCount = $itemInboxModel->getLatestItemCount($userId, $filteredData['minDate']);

            if ($totalCount == 0) {
                $this->view->success    = true;
                $this->view->error      = null;
                $this->view->items      = array();
                $this->view->version    = 1;
                $this->view->totalCount = 0;
                return;
            }

            $decoratedModel = new Intrabuild_BeanContext_Decorator(
                $itemInboxModel,
                $itemResponseFilter
            );

            $rows = $decoratedModel->getLatestEmailItemsForAsDto(
                $userId,
                $filteredData['minDate'],
                $sortInfo
            );
        }

        $this->view->success      = true;
        $this->view->error        = null;
        $this->view->items        = $rows;
        $this->view->pendingItems = $pendingItems;
        $this->view->version      = 1;
        $this->view->totalCount   = $totalCount;
    }


    /**
     * Flags one or more specific email items as either read/unread or spam/nospam,
     * based on the passed arguments.
     * The if the type-parameter is available, the update-action will either
     * respect the "is_read" property or the "is_spam" property.
     * type == spam
     * will update the is_spam property
     * type == read
     * will update the is_read property
     *
     * Data will be comin via post, whereas in json context a json-encoded
     * string will be submitted, which can be found in the $_POST var keyed
     * with "json".
     * The method will never return an error itself, as the operation on the underlying
     * datastore will not affect userinteraction critically.
     */
    public function setEmailFlagAction()
    {
        if ($this->_helper->contextSwitch()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toUpdate = Zend_Json::decode($_POST['json'], Zend_Json::TYPE_ARRAY);
        }

        require_once 'Intrabuild/Keys.php';
        require_once 'Intrabuild/Modules/Groupware/Email/Item/Filter/Flag.php';
        require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Flag.php';

        $auth   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        // set the filter context based on the passed type-parameter
        $type = isset($_POST['type']) ? trim(strtolower($_POST['type'])) : 'null';
        $CONTEXT_READ = Intrabuild_Modules_Groupware_Email_Item_Filter_Flag::CONTEXT_READ;
        $CONTEXT_SPAM = Intrabuild_Modules_Groupware_Email_Item_Filter_Flag::CONTEXT_SPAM;
        switch ($type) {
            case 'read':
                $context = $CONTEXT_READ;
            break;
            case 'spam':
                $context = $CONTEXT_SPAM;
            break;
            default:
                return;
        }

        $model  = new Intrabuild_Modules_Groupware_Email_Item_Model_Flag();
        $filter = new Intrabuild_Modules_Groupware_Email_Item_Filter_Flag(array(), $context);

        for ($i = 0, $len = count($toUpdate); $i < $len; $i ++) {
            $filter->setData($toUpdate[$i]);
            $data = $filter->getProcessedData();

            if ($context == $CONTEXT_READ) {
                $model->flagItemAsRead($data['id'], $userId, $data['isRead']);
            } else if ($context == $CONTEXT_SPAM) {
                $model->flagItemAsSpam($data['id'], $userId, $data['isSpam']);
            }
        }

        $this->view->success = true;
        $this->view->error   = null;

    }

    /**
     * Returns an email for the specified id (POST).
     *
     *
     */
    public function getEmailAction()
    {
        require_once 'Intrabuild/BeanContext/Decorator.php';
        require_once 'Intrabuild/Modules/Groupware/Email/Message/Filter/MessageResponse.php';
        require_once 'Intrabuild/Keys.php';


        $auth   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $messageDecorator = new Intrabuild_BeanContext_Decorator(
            'Intrabuild_Modules_Groupware_Email_Message_Model_Message',
            new Intrabuild_Modules_Groupware_Email_Message_Filter_MessageResponse(
                array(),
                Intrabuild_Filter_Input::CONTEXT_RESPONSE
            )
        );

        $groupwareEmailItemsId = (int)$_POST['id'];

        $message = $messageDecorator->getEmailMessageAsDto($groupwareEmailItemsId, $userId);

        if (!$message) {
            $this->view->success    = true;
            $this->view->error      = null;
            $this->view->item       = array(array());
            return;
        }

        require_once 'Intrabuild/Modules/Groupware/Email/Attachment/Filter/AttachmentResponse.php';

        $attachmentDecorator = new Intrabuild_BeanContext_Decorator(
            'Intrabuild_Modules_Groupware_Email_Attachment_Model_Attachment',
            new Intrabuild_Modules_Groupware_Email_Attachment_Filter_AttachmentResponse(
                array(),
                Intrabuild_Filter_Input::CONTEXT_RESPONSE
            )
        );

        $attachments = $attachmentDecorator->getAttachmentsForItemAsDto($groupwareEmailItemsId);

        $message->attachments = $attachments;

        $this->view->success    = true;
        $this->view->error      = null;
        $this->view->item       = array($message);
    }


// -------- email accounts

    /**
     * This action will update email accounts according to the POST params
     * that come along with the request.
     * Depending of the current context the action was called, the format of the
     * POST params will differ: For json, it will be an array holding json-encoded parameters.
     * Despite all different formats the POST params may come in, each request sends
     * values for 'updated' and 'deleted' accounts: 'deleted' as an array, holding all
     * ids that should become deleted, and 'updated' an array holding information about
     * the records that get edited, represented by specific associative array, representing
     * the fields of the record.
     * The view will await values assigned to 'updated_failed' and 'deleted_failed', whereas
     * 'deleted_failed' is an array containing all ids that couldn't be deleted, and
     * 'updated_failed' an array containing the records (in associative array notation)
     * that could not be updated.
     *
     * Note: If any error in the user-input was detected, no update-action will happen, but
     * deltes may already have been submitted to the underlying datastore.
     * The first error found in the passed data will be returned as an error of the type
     * Intrabuild_Error_Form, containing the fields that where errorneous.
     *
     */
    public function updateEmailAccountsAction()
    {
        require_once 'Intrabuild/Modules/Groupware/Email/AccountFilter.php';
        require_once 'Intrabuild/Util/Array.php';
        require_once 'Intrabuild/Modules/Groupware/Email/AccountModel.php';

        $toDelete      = array();
        $toUpdate      = array();
        $deletedFailed = array();
        $updatedFailed = array();

        $model   = new Intrabuild_Modules_Groupware_Email_AccountModel();
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
            $filter = new Intrabuild_Modules_Groupware_Email_AccountFilter($_, Intrabuild_Filter_Input::CONTEXT_UPDATE);
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
     * Reads out all email accounts belonging to the currently logged in user and
     * assigns them to the view variables, using the appropriate dto.
     * The format will differ from the actual context the action was requested
     * (e.g. context json will assign json encoded strings to the view variables).
     *
     * Passwords set for this account will be masked with "*".
     */
    public function getEmailAccountsAction()
    {
        require_once 'Intrabuild/Keys.php';
        $user = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();

        require_once 'Intrabuild/BeanContext/Decorator.php';
        $decoratedModel = new Intrabuild_BeanContext_Decorator(
            'Intrabuild_Modules_Groupware_Email_Account_Model_Account'
        );

        $data = $decoratedModel->getAccountsForUserAsDto($user->getId());

        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $dto =& $data[$i];
            if (!$dto->isOutboxAuth) {
                $dto->usernameOutbox = "";
                $dto->passwordOutbox = "";
            }
            $dto->passwordOutbox = str_pad("", strlen($dto->passwordOutbox), '*');
            $dto->passwordInbox  = str_pad("", strlen($dto->passwordInbox), '*');
        }

        $this->view->success  = true;
        $this->view->accounts = $data;
        $this->view->error    = null;
    }

    /**
     * Adds an email account to the database, assigning it to the currently
     * logged in user.
     * The following key/value pairs will be submitted via POST:
     *
     * <ul>
     * <li>name           : The name of the account</li>
     * <li>address        : The email address for this account</li>
     * <li>isStandard    : <tt>true</tt>, if this should become the standard email account,
     * otherwise <tt>false</tt></li>
     * <li>server_inbox   : The address of the inbox-server</li>
     * <li>server_outbox  : The address of the outbox server</li>
     * <li>username_inbox : The username for the inbox-server</li>
     * <li>username_outbox: The username for the outbox-server. If <tt>outbox_auth</tt>
     * equals to <tt>false</tt>, the value will be empty</li>
     * <li>user_name      : The full name of the user who owns this account</li>
     * <li>outbox_auth    : <tt>true</tt> if the outbox-server needs authentication,
     * otherwise <tt>false</tt></li>
     * <li>password_inbox : The password for the inbox-server</li>
     * <li>password_outbox: The password for the outbox-server. If <tt>outbox_auth</tt>
     * equals to <tt>false</tt>, the value will be empty</li>
     * </ul>
     *
     * Upon success, the following view variables will be assigned:
     * <ul>
     *  <li>success: <tt>true</tt>, if the account was added to the database,
     *  otherwise <tt>false></tt></li>
     *  <li>account: a fully configured instance of <tt>Intrabuild_Groupware_Email_Account</tt>.
     * <br /><strong>NOTE:</strong> If the user submitted passwords, those will be replaced by strings
     * containing only blanks, matching the length of the originally submitted
     * passwords.</li>
     * <li>error: An object of the type <tt>Intrabuild_Groupware_ErrorObject</tt>, if any error
     * occured, otherwise <tt>null</tt></li>
     * <ul>
     *
     * <strong>Note:</strong> The properties <tt>account</tt> and <tt>error</tt> will
     * be returned in the format based on the passed context the action was called.
     * For example, if an array was assigned to <tt>account</tt> and the context is <tt>json</tt>,
     * this array will become json-encoded and returned as a string. This happens transparently.
     */
    public function addEmailAccountAction()
    {
        require_once 'Intrabuild/Util/Array.php';
        require_once 'Intrabuild/Keys.php';
        require_once 'Intrabuild/BeanContext/Inspector.php';
        require_once 'Intrabuild/Modules/Groupware/Email/AccountModel.php';
        require_once 'Intrabuild/Modules/Groupware/Email/AccountFilter.php';

        $model  = new Intrabuild_Modules_Groupware_Email_AccountModel();
        $filter = new Intrabuild_Modules_Groupware_Email_AccountFilter($_POST, Intrabuild_Filter_Input::CONTEXT_CREATE);

        $auth   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $classToCreate = 'Intrabuild_Modules_Groupware_Email_Account';

        $this->view->success = true;
        $this->view->error = null;

        try {
            $processedData = $filter->getProcessedData();
            $data = Intrabuild_Util_Array::underscoreKeys($processedData);
            $data['user_id'] = $userId;
            $processedData['id'] = $model->insert($data);
            $processedData['userId'] = $userId;
            $processedData['passwordInbox']  = str_pad("", strlen($processedData['passwordInbox']), '*');
            if ($processedData['isOutboxAuth']) {
                $processedData['passwordOutbox'] = str_pad("", strlen($processedData['passwordOutbox']), '*');
            }
            $this->view->account = Intrabuild_BeanContext_Inspector::create($classToCreate, $processedData)->getDto();
        } catch (Zend_Filter_Exception $e) {
            require_once 'Intrabuild/Error.php';
            $error = Intrabuild_Error::fromFilter($filter, $e);
            $accountData = $_POST;
            $accountData['passwordOutbox'] = str_pad("", strlen($accountData['passwordOutbox']), '*');
            $accountData['passwordInbox']  = str_pad("", strlen($accountData['passwordInbox']), '*');
            $this->view->account = Intrabuild_BeanContext_Inspector::create($classToCreate, $accountData)->getDto();
            $this->view->success = false;
            $this->view->error = $error->getDto();
        }
    }


    /**
     *
     */
    public function moveToOutboxAction()
    {
        require_once 'Zend/Json.php';

        $response = array('response'  => array(
                              'type'  => 'integer',
                              'value' =>  rand(1, 100000)
                        ));

        $json = Zend_Json::encode($response, Zend_Json::TYPE_ARRAY);

        echo $json;
        die();

    }

    /**
     *
     */
    public function sendAction()
    {
        require_once 'Zend/Json.php';

        $response = array('response'  => array(
                              'type'  => 'integer',
                              'value' =>  rand(1, 100000)
                        ));

        $json = Zend_Json::encode($response, Zend_Json::TYPE_ARRAY);

        echo $json;
        die();

    }


    /**
     *
     */
    public function saveDraftAction()
    {
        require_once 'Zend/Json.php';

        $id = $_POST['id'];

        $id = $id == -1 ? rand(1, 1000000) : $id;

        $response = array('response'  => array(
                              'type'  => 'integer',
                              'value' => $id
                        ));

        $json = Zend_Json::encode($response, Zend_Json::TYPE_ARRAY);

        echo $json;
        die();

    }


    /**
     * A draft can be loaded from the database if an id was supplied
     * or filled with dummy data if no id was supplied. If no id was supplied,
     * the user wants to create a new email. In this case, the id defaults to
     * -1. If the user requests to save the draft later on, the id will be updated
     * to the value of teh auto_increment field of the table.
     * Along with an id the application will need a folder_id so it can tell wether
     * an existing view has to be updated if this draft was edited and the folder
     * is currently visible.
     * Note, that getDraft will also be executed when the user wants to reply to
     * an email or forward an email. in this case, the id defaults to the email to
     * which the user wants to forward/ reply to.
     */
    public function getDraftAction()
    {
    	$id = $_POST['id'];

    	if ($id == -1) {
    	    $this->view->success = true;
    	    $this->view->draft   = array(
                'id'         => -1,
                'message'    => '',
                'groupwareEmailAccountsId' => 1,
                'groupwareEmailFoldersId'  => -1,
                'subject'    => '',
                'recipients' => array(
                	'to' => array('')
                )
            );
            $this->view->error = null;
	    } else {

	    	$prefix = "";

	    	switch ($_POST['type']) {
				case 'reply':     $prefix = 'Re: '; break;
				case 'reply_all': $prefix = 'Re: '; break;
				case 'forward':   $prefix = 'Fwd: '; break;
	    	}

	    	$msg = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh          <br/>
euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad   <br/>
minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut         <br/>
aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in           <br/>
vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis           <br/>
at vero et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril                <br/>
<blockquote>delenit augue duis dolore <blockqoute>te feugait nulla facilisi. Lorem ipsum dolor sit amet,<br/>
consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet           <br/>
dolore <blockquote>magna <blockquote>aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud             <br/>
exerci tation ullamcorper suscipit lo</blockquote>bortis nisl ut aliquip ex ea commodo consequat.<br />
Duis</blockquote> autem vel eum </blockquote>iriure dolor in hendrerit in vulputate velit esse molestie consequat,<br />
</blockquote>vel illum dolore eu feugiat nulla facilisis at vero et accumsan et iusto odio dignissim<br />
qui blandit praesent luptatum zzril  delenit augue duis dolore te feugait nulla facilisi.<br />
Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod<br />
mazim placerat facer possim assum.";

            $this->view->success = true;
            $clRep = md5(time());
            $this->view->draft   = array(
                'id'              => $id,
                'classSubstitute' => $clRep,
                'message'         => '<blockquote class="'.$clRep.'">'.$msg.'</blockquote>',
                'groupwareEmailAccountsId'      => 1,
                'groupwareEmailFoldersId'       => -1,
                'subject'         => $prefix . 'Keine neue Email <YO>',
                'recipients'      => array(
                    'to'  => array('test@test.de', 'test2@test2.de'),
                    'cc'  => array('test3@test3de', 'test4@test4.de'),
                    'bcc' => array('test5@test5de', 'test6@test6.de')
                )
            );
            $this->view->error = null;
	    }
    }

    public function getRecipientAction()
    {
        if (isset($_POST['query'])) {

            $options['response']['value'][]['text'] = $_POST['query']." <".str_replace(" ", "", $_POST['query'])."@testdomain.co.uk.org.de>";

        } else {
            $options = array('response' => array(
                                               'type'  => 'array',
                                               'value' => array(
                                                array())));
        }

        Zend_Loader::loadClass('Zend_Json');
        $json = Zend_Json::encode($options, Zend_Json::TYPE_ARRAY);

        echo $json;
        die();
    }


    public function getEmailFormRecipientsAction()
    {
        //if (isset($_POST['id']))
        $options = array('response' => array(
                                           'type'  => 'array',
                                           'value' => array(
                                            array(
                                                'receive_type'   => 'to',
                                                'address' => ''
                                       ))));

        Zend_Loader::loadClass('Zend_Json');
        $json = Zend_Json::encode($options, Zend_Json::TYPE_ARRAY);

        echo $json;
        die();
    }

    public function getReceiveTypesAction()
    {
        $options = array('response' => array(
                                           'type'  => 'array',
                                           'value' => array(
                                            array(
                                                'id'   => 'to',
                                                'text' => 'An:'
                                            ), array(
                                                'id'   => 'cc',
                                                'text' => 'CC:'
                                             ), array(
                                                'id'   => 'bcc',
                                                'text' => 'BCC:'
                                       ))));

        Zend_Loader::loadClass('Zend_Json');
        $json = Zend_Json::encode($options, Zend_Json::TYPE_ARRAY);

        echo $json;
        die();
    }



}