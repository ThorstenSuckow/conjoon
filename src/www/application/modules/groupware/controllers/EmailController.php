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
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext
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
                       ->addActionContext('get.recipient', self::CONTEXT_JSON)
                       ->addActionContext('get.draft', self::CONTEXT_JSON)
                       ->addActionContext('save.draft', self::CONTEXT_JSON)
                       ->addActionContext('move.to.outbox', self::CONTEXT_JSON)
                       // send emails
                       ->addActionContext('send', self::CONTEXT_JSON)
                       ->addActionContext('bulk.send', self::CONTEXT_JSON)
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
            if ($accountId < 0) {
                $this->view->success    = true;
                $this->view->totalCount = 0;
                $this->view->items      = array();
                $this->view->error      = null;
                return;
            } else if ($accountId == 0) {
                $accountId = null;
            }
        } else {
            $accountId = null;
        }

        require_once 'Conjoon/Keys.php';
        require_once 'Conjoon/Modules/Groupware/Email/Letterman.php';
        require_once 'Conjoon/BeanContext/Decorator.php';
        require_once 'Conjoon/Error.php';

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $emails        = array();
        $errorMessages = array();

        $time = time();
        $currentAccount = null;
        try {
            $accountDecorator = new Conjoon_BeanContext_Decorator(
                'Conjoon_Modules_Groupware_Email_Account_Model_Account'
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
                $currentAccount =& $accounts[$i];
                $tmpEmails = Conjoon_Modules_Groupware_Email_Letterman::fetchEmails($userId, $currentAccount);

                $emails        = array_merge($emails, $tmpEmails['fetched']);
                $errorMessages = array_merge($errorMessages, $tmpEmails['errors']);
            }


        } catch (Zend_Mail_Protocol_Exception $e) {
            $errorMessages[] = $e->getMessage()."\n - host: ".
                               $currentAccount->getServerInbox().':'.
                               $currentAccount->getPortInbox()."\n user: ".
                               $currentAccount->getUsernameInbox()." (using password: ".
                               (strlen($currentAccount->getPasswordInbox()) > 0 ? 'yes' : 'no').')';
        }

        $len = count($emails);
        if ($len > 0) {
            /**
             * @see Conjoon_BeanContext_Decorator
             */
            require_once 'Conjoon/BeanContext/Decorator.php';

            /**
             * @see Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse
             */
            require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ItemResponse.php';

            $max = 150;

            if ($len >= $max) {
                $modelClass = 'Conjoon_Modules_Groupware_Email_Item_Model_Inbox';
            } else {
                $modelClass = 'Conjoon_Modules_Groupware_Email_Item_Model_Item';
            }

            $decoratedModel = new Conjoon_BeanContext_Decorator(
                $modelClass,
                new Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse(
                    array(),
                    Conjoon_Filter_Input::CONTEXT_RESPONSE
                )
            );

            if ($len >= $max) {
                $emails = $decoratedModel->getLatestEmailItemsForAsDto(
                    $userId, $time,
                    array(
                        'start' => 0,
                        'limit' => $len,
                        'dir'   => 'ASC',
                        'sort'  => 'id'
                    )
                );
            } else {
                $emails = $decoratedModel->getItemsForUserAsDto(
                    $emails, $userId,
                    array(
                        'dir'   => 'ASC',
                        'sort'  => 'id'
                    )
                );
            }
        }

        $this->view->success    = true;
        // count again, since during db operation old records might have
        // been deleted from the db
        $this->view->totalCount = count($emails);
        $this->view->items      = $emails;
        $this->view->error      = null;

        if (count($errorMessages) > 0) {
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while fetching email(s)';
            $error->message = implode('<br />', $errorMessages);
            $error->level = Conjoon_Error::LEVEL_ERROR;
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
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_DELETE
        );

        $filteredData = array();
        try {
            $filteredData = $filter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            require_once 'Conjoon/Error.php';
            $error = Conjoon_Error::fromFilter($filter, $e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();

        require_once 'Conjoon/Keys.php';
        $user   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        $userId = $user->getId();

        $ret = $folderModel->deleteFolder($filteredData['id'], $userId);

        if ($ret === 0) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();
            $error->title   = 'Error';
            $error->level   = Conjoon_Error::LEVEL_WARNING;
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
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_MOVE
        );

        $filteredData = array();
        try {
            $filteredData = $filter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            require_once 'Conjoon/Error.php';
            $error = Conjoon_Error::fromFilter($filter, $e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();

        $ret = $folderModel->moveFolder($filteredData['id'], $filteredData['parentId']);

        if ($ret === 0) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();
            $error->title   = 'Error';
            $error->level   = Conjoon_Error::LEVEL_WARNING;
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
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_CREATE
        );

        $filteredData = array();
        try {
            $filteredData = $filter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            require_once 'Conjoon/Error.php';
            $error = Conjoon_Error::fromFilter($filter, $e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();

        require_once 'Conjoon/Keys.php';
        $user   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        $userId = $user->getId();

        $id = $folderModel->addFolder($filteredData['parentId'], $filteredData['name'], $userId);

        if ((int)$id <= 0) {
            $this->view->success = false;
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();
            $error->file  = __FILE__;
            $error->line  = __LINE__;
            $error->type  = Conjoon_Error::UNKNOWN;
            $error->level = Conjoon_Error::LEVEL_WARNING;
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
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_RENAME
        );

        $filteredData = array();
        try {
            $filteredData = $filter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            require_once 'Conjoon/Error.php';
            $error = Conjoon_Error::fromFilter($filter, $e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();

        $ret = $folderModel->renameFolder($filteredData['id'], $filteredData['name']);

        if ($ret === 0) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();
            $error->title   = 'Error';
            $error->level   = Conjoon_Error::LEVEL_WARNING;
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

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedFolderModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Folder_Model_Folder'
        );

        require_once 'Conjoon/Keys.php';
        $user   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
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
        if ($this->_helper->conjoonContext()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toDelete = Zend_Json::decode($_POST['itemsToDelete'], Zend_Json::TYPE_ARRAY);
        }

        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/Item.php';

        $filter = new Conjoon_Modules_Groupware_Email_Item_Filter_Item(
            array(),
            Conjoon_Modules_Groupware_Email_Item_Filter_Item::CONTEXT_DELETE
        );

        $itemIds = array();
        for ($i = 0, $len = count($toDelete); $i < $len; $i++) {
            $filteredData = $filter->setData($toDelete[$i])->getProcessedData();
            $itemIds[] = $filteredData['id'];
        }

        require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Item.php';
        require_once 'Conjoon/Keys.php';
        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $model = new Conjoon_Modules_Groupware_Email_Item_Model_Item();

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
        if ($this->_helper->conjoonContext()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toMove = Zend_Json::decode($_POST['itemsToMove'], Zend_Json::TYPE_ARRAY);
        }

        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/Item.php';

        $filter = new Conjoon_Modules_Groupware_Email_Item_Filter_Item(
            array(),
            Conjoon_Modules_Groupware_Email_Item_Filter_Item::CONTEXT_MOVE
        );

        $moveData = array();
        for ($i = 0, $len = count($toMove); $i < $len; $i++) {
            $filteredData = $filter->setData($toMove[$i])->getProcessedData();
            if (!isset($moveData[$filteredData['groupwareEmailFoldersId']])) {
                $moveData[$filteredData['groupwareEmailFoldersId']] = array();
            }
            $moveData[$filteredData['groupwareEmailFoldersId']][] = $filteredData['id'];
        }

        require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Item.php';
        $model = new Conjoon_Modules_Groupware_Email_Item_Model_Item();

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
     *         Conjoon_Modules_Groupware_Email_ItemDto-class and have to be substituted
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
     * items - an array with objects of Conjoon_Modules_Groupware_Email_ItemDto
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
        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/Request.php';

        require_once 'Conjoon/Util/Array.php';
        require_once 'Conjoon/Keys.php';

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $CONTEXT_REQUEST_LATEST = Conjoon_Modules_Groupware_Email_Item_Filter_Request::CONTEXT_REQUEST_LATEST;
        $CONTEXT_REQUEST        = Conjoon_Modules_Groupware_Email_Item_Filter_Request::CONTEXT_REQUEST;

        if (isset($_POST['minDate']) && !isset($_POST['groupwareEmailFoldersId'])) {
            $context = $CONTEXT_REQUEST_LATEST;
        } else {
            $context = $CONTEXT_REQUEST;
        }

        $itemRequestFilter = new Conjoon_Modules_Groupware_Email_Item_Filter_Request(
            $_POST,
            $context
        );

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



        require_once 'Conjoon/BeanContext/Decorator.php';
        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ItemResponse.php';
        $itemResponseFilter = new Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse(
            array(),
            Conjoon_Filter_Input::CONTEXT_RESPONSE
        );

        $pendingItems = -1;

        if ($context == $CONTEXT_REQUEST) {

            // get the number of emails currently available for this folder
            // and this user
            require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Item.php';
            require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';

            $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();
            $itemModel   = new Conjoon_Modules_Groupware_Email_Item_Model_Item();

            $totalCount  = $folderModel->getItemCountForFolderAndUser(
                $filteredData['groupwareEmailFoldersId'], $userId
            );

            if ($totalCount == 0) {
                $this->view->success      = true;
                $this->view->error        = null;
                $this->view->items        = array();
                $this->view->version      = 1;
                $this->view->totalCount   = 0;
                $this->view->pendingItems = 0;
                return;
            }

            $decoratedModel = new Conjoon_BeanContext_Decorator(
                $itemModel,
                $itemResponseFilter
            );

            $rows = $decoratedModel->getEmailItemsForAsDto(
                $userId,
                $filteredData['groupwareEmailFoldersId'],
                $sortInfo
            );

            $pendingItems = $folderModel->getPendingCountForFolderAndUser(
                $filteredData['groupwareEmailFoldersId'], $userId
            );


        } else if ($context == $CONTEXT_REQUEST_LATEST) {

            require_once 'Conjoon/BeanContext/Decorator.php';
            require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ItemResponse.php';
            require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Inbox.php';

            $itemInboxModel = new Conjoon_Modules_Groupware_Email_Item_Model_Inbox();
            $totalCount = $itemInboxModel->getLatestItemCount($userId, $filteredData['minDate']);

            if ($totalCount == 0) {
                $this->view->success    = true;
                $this->view->error      = null;
                $this->view->items      = array();
                $this->view->version    = 1;
                $this->view->totalCount = 0;
                return;
            }

            $decoratedModel = new Conjoon_BeanContext_Decorator(
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
        if ($this->_helper->conjoonContext()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toUpdate = Zend_Json::decode($_POST['json'], Zend_Json::TYPE_ARRAY);
        }

        require_once 'Conjoon/Keys.php';
        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/Flag.php';
        require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Flag.php';

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        // set the filter context based on the passed type-parameter
        $type = isset($_POST['type']) ? trim(strtolower($_POST['type'])) : 'null';
        $CONTEXT_READ = Conjoon_Modules_Groupware_Email_Item_Filter_Flag::CONTEXT_READ;
        $CONTEXT_SPAM = Conjoon_Modules_Groupware_Email_Item_Filter_Flag::CONTEXT_SPAM;
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

        $model  = new Conjoon_Modules_Groupware_Email_Item_Model_Flag();
        $filter = new Conjoon_Modules_Groupware_Email_Item_Filter_Flag(array(), $context);

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
        $message = $this->_getEmail((int)$_POST['id']);

        if (!$message) {
            $this->view->success    = true;
            $this->view->error      = null;
            $this->view->item       = null;
            return;
        }

        $this->view->success     = true;
        $this->view->error       = null;
        $this->view->item        = $message;
    }

    /**
     * @todo
     */
    private function _getEmail($groupwareEmailItemsId, $refreshCache = false)
    {
        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        /**
         * @see Conjoon_Builder_Factory
         */
        require_once 'Conjoon/Builder/Factory.php';

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $builder = Conjoon_Builder_Factory::getBuilder(
            Conjoon_Keys::CACHE_EMAIL_MESSAGE,
            Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
        );

        if ($refreshCache === true) {
            $builder->remove(array(
                'groupwareEmailItemsId' => $groupwareEmailItemsId,
                'userId'                => $userId
            ));
        }

        return $builder->get(array(
            'groupwareEmailItemsId' => $groupwareEmailItemsId,
            'userId'                => $userId
        ));

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
     * Conjoon_Error_Form, containing the fields that where errorneous.
     *
     */
    public function updateEmailAccountsAction()
    {
        require_once 'Conjoon/Modules/Groupware/Email/Account/Filter/Account.php';
        require_once 'Conjoon/Util/Array.php';
        require_once 'Conjoon/Modules/Groupware/Email/Account/Model/Account.php';

        $toDelete      = array();
        $toUpdate      = array();
        $deletedFailed = array();
        $updatedFailed = array();

        $model   = new Conjoon_Modules_Groupware_Email_Account_Model_Account();

        $data  = array();
        $error = null;

        if ($this->_helper->conjoonContext()->getCurrentContext() == self::CONTEXT_JSON) {
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
            $filter = new Conjoon_Modules_Groupware_Email_Account_Filter_Account(
                $_,
                Conjoon_Filter_Input::CONTEXT_UPDATE
            );
            try {
                $data[$i] = $filter->getProcessedData();
                Conjoon_Util_Array::underscoreKeys($data[$i]);
            } catch (Zend_Filter_Exception $e) {
                 require_once 'Conjoon/Error.php';
                 $error = Conjoon_Error::fromFilter($filter, $e);
                 $this->view->success = false;
                 $this->view->updatedFailed = array($_['id']);
                 $this->view->deletedFailed = $deletedFailed;
                 $this->view->error = $error->getDto();
                 return;
            }
        }

        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $id = $data[$i]['id'];
            unset($data[$i]['id']);
            $affected = $model->updateAccount($id, $data[$i]);
            if ($affected == -1) {
                $updatedFailed[] = $id;
            }
        }

        $this->view->success        = empty($updatedFailed) ? true : false;
        $this->view->updatedFailed = $updatedFailed;
        $this->view->deletedFailed = $deletedFailed;
        $this->view->error         = null;

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
        require_once 'Conjoon/Keys.php';
        $user = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Account_Model_Account'
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
     *  <li>account: a fully configured instance of <tt>Conjoon_Groupware_Email_Account</tt>.
     * <br /><strong>NOTE:</strong> If the user submitted passwords, those will be replaced by strings
     * containing only blanks, matching the length of the originally submitted
     * passwords.</li>
     * <li>error: An object of the type <tt>Conjoon_Groupware_ErrorObject</tt>, if any error
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
        require_once 'Conjoon/Util/Array.php';
        require_once 'Conjoon/Keys.php';
        require_once 'Conjoon/BeanContext/Inspector.php';
        require_once 'Conjoon/Modules/Groupware/Email/Account/Model/Account.php';
        require_once 'Conjoon/Modules/Groupware/Email/Account/Filter/Account.php';

        $model  = new Conjoon_Modules_Groupware_Email_Account_Model_Account();
        $filter = new Conjoon_Modules_Groupware_Email_Account_Filter_Account(
            array(),
            Conjoon_Filter_Input::CONTEXT_CREATE
        );

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $classToCreate = 'Conjoon_Modules_Groupware_Email_Account';

        $this->view->success = true;
        $this->view->error = null;

        try {
            $filter->setData($_POST);
            $processedData = $filter->getProcessedData();
            $data = $processedData;
            Conjoon_Util_Array::underscoreKeys($data);
            $addedId = $model->addAccount($userId, $data);

            /**
             * @see Conjoon_BeanContext_Decorator
             */
            require_once 'Conjoon/BeanContext/Decorator.php';

            $decoratedModel = new Conjoon_BeanContext_Decorator(
                'Conjoon_Modules_Groupware_Email_Account_Model_Account'
            );

            $dto = $decoratedModel->getAccountAsDto($addedId, $userId);

            if (!$dto->isOutboxAuth) {
                $dto->usernameOutbox = "";
                $dto->passwordOutbox = "";
            }
            $dto->passwordOutbox = str_pad("", strlen($dto->passwordOutbox), '*');
            $dto->passwordInbox  = str_pad("", strlen($dto->passwordInbox), '*');

            $this->view->account = $dto;

        } catch (Zend_Filter_Exception $e) {
            require_once 'Conjoon/Error.php';
            $error = Conjoon_Error::fromFilter($filter, $e);
            $accountData = $_POST;
            $accountData['passwordOutbox'] = isset($accountData['passwordOutbox'])
                                             ? str_pad("", strlen($accountData['passwordOutbox']), '*')
                                             : '';
            $accountData['passwordInbox'] = isset($accountData['passwordInbox'])
                                            ? str_pad("", strlen($accountData['passwordInbox']), '*')
                                            : '';
            $this->view->account = Conjoon_BeanContext_Inspector::create(
                $classToCreate,
                $accountData
            )->getDto();
            $this->view->success = false;
            $this->view->error = $error->getDto();
        }
    }

// -------- email drafts

    /**
     * Looks up a recipient from the contacts table and returns it to the view.
     * The value submitted is a fragment of either a contacts real name or the
     * email address of the contact.
     *
     *
     */
    public function getRecipientAction()
    {
        require_once 'Conjoon/Util/Array.php';
        require_once 'Conjoon/Keys.php';
        require_once 'Conjoon/BeanContext/Inspector.php';
        require_once 'Conjoon/Modules/Groupware/Contact/Item/Model/Item.php';

        $query = isset($_POST['query']) ? $_POST['query'] : '';

        if (trim($query) == "") {
            $this->view->success = true;
            $this->view->error   = null;
            $this->view->matches = array();
            return;
        }

        $model  = new Conjoon_Modules_Groupware_Contact_Item_Model_Item();

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $contacts = $model->getContactsByNameOrEmailAddress($userId, $query);

        $response = array();

        foreach ($contacts as $contact) {
            $address = $contact['email_address'];
            $name    = ($contact['first_name']
                       ? $contact['first_name'] .' '
                       : '')
                       . ($contact['last_name']
                       ? $contact['last_name'] .' '
                       : '');

            $response[] = array(
                'name'        => $name ? $name : '',
                'address'     => $address,
                'fullAddress' => $name ? $name . '<' . $address . '>' : $address
            );
        }

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->matches = $response;
    }

    /**
     * Sends an email to the specified recipients.
     * The action expects the following arguments to be passed:
     *
     * - format: The format the email should be send. Can default to
     *           "text/plain", "text/html" - or "multipart" if the email should
     *           be send both as html and plain-text.
     * - id: The id of the messge if this was loaded from an already existing
     *       draft (i.e. a draft, an email that is being replied to which is being forwarded).
     * -type: The type of teh action: if this equals to "reply", "reply_all" or "forward",
     * this message references an existing one
     * Can default to 0 or -1 if the emil was created from the scratch
     * - groupwareEmailAccountsId: An integer specifying the id of the email account of the
     *              user which will be used to send the message
     * - groupwareEmailFoldersId: The id of the folder from which this email was opened. Equals
     *             to -1 or 0 if the messge was created from scratch
     * - subject: The subject of the message
     * - message: The message as edited in the browser. Will most likely have
     *            HTML tags in it depending on the editor used
     * - to: An json encoded array with all addresses being specified in the "to"
     *       field. Addresses may be separated by a comma "," or a semicolon ";"
     * - cc: An json encoded array with all addresses being specified in the "cc"
     *       field. Addresses may be separated by a comma "," or a semicolon ";"
     * - bcc: An json encoded array with all addresses being specified in the "bcc"
     *        field. Addresses may be separated by a comma "," or a semicolon ";"
     *
     * The view awaits a fully configured email item as the response.
     */
    public function sendAction()
    {
        require_once 'Conjoon/Modules/Groupware/Email/Draft/Filter/DraftInput.php';

        $data = array();
        try {
            // the filter will transform the "message" into bodyHtml and bodyText, depending
            // on the passed format. both will only be filled if format equals to "multipart"
            $filter = new Conjoon_Modules_Groupware_Email_Draft_Filter_DraftInput(
                $_POST,
                Conjoon_Filter_Input::CONTEXT_CREATE
            );
            $data = $filter->getProcessedData();
        } catch (Exception $e) {
             require_once 'Conjoon/Error.php';
             $error = Conjoon_Error::fromFilter($filter, $e);
             $this->view->success = false;
             $this->view->error   = $error->getDto();
             $this->view->item    = null;
             return;
        }


        require_once 'Conjoon/Modules/Groupware/Email/Address.php';
        require_once 'Conjoon/Modules/Groupware/Email/Draft.php';
        require_once 'Conjoon/BeanContext/Inspector.php';

        // create the message object here
        $to  = array();
        $cc  = array();
        $bcc = array();

        $toString  = array();
        $ccString  = array();

        foreach ($data['cc'] as $dcc) {
            $add        = new Conjoon_Modules_Groupware_Email_Address($dcc);
            $cc[]       = $add;
            $toString[] = $add->__toString();
        }
        foreach ($data['bcc'] as $dbcc) {
            $add         = new Conjoon_Modules_Groupware_Email_Address($dbcc);
            $bcc[]       = $add;
        }
        foreach ($data['to'] as $dto) {
            $add        = new Conjoon_Modules_Groupware_Email_Address($dto);
            $to[]       = $add;
            $toString[] = $add->__toString();
        }

        $toString  = implode(', ', $toString);
        $ccString  = implode(', ', $ccString);

        $data['cc']  = $cc;
        $data['to']  = $to;
        $data['bcc'] = $bcc;

        // get the specified account for the user
        require_once 'Conjoon/BeanContext/Decorator.php';
        require_once 'Conjoon/Keys.php';

        $accountDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Account_Model_Account'
        );

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $account = $accountDecorator->getAccountAsEntity($data['groupwareEmailAccountsId'], $userId);

        // no account found?
        if (!$account) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while sending email';
            $error->message = 'Could not find specified account.';
            $error->level = Conjoon_Error::LEVEL_ERROR;
            $this->view->error   = $error;
            $this->view->success = false;
            $this->view->item    = null;
            return;
        }

        $message = Conjoon_BeanContext_Inspector::create(
                'Conjoon_Modules_Groupware_Email_Draft',
                $data,
                true
        );

        require_once 'Conjoon/Modules/Groupware/Email/Sender.php';

        try {
            $mail = Conjoon_Modules_Groupware_Email_Sender::send($message, $account);
        } catch (Exception $e) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while sending email';
            $error->message = $e->getMessage();
            // check here if a message is set. We rely heavily on stream_socket_client
            // in Zend_Mail_Protocol_Abstract which may not set the error message all
            // the time. If no internet conn is available, the message will be missing
            // on windows systems, for example
            if ($error->message == "") {
                $error->message = "The message with the subject \""
                                  . $message->getSubject()."\" could not be sent. "
                                  . "Please check the internet connection of "
                                  . "the server this software runs on.";
            }
            $error->level = Conjoon_Error::LEVEL_ERROR;
            $this->view->error   = $error;
            $this->view->success = false;
            $this->view->item    = null;
            return;
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ItemResponse.php';

        // if the email was send successfully, save it into the db and
        // return the params savedId (id of the newly saved email)
        // and savedFolderId (id of the folder where the email was saved in)
        $itemDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Item_Model_Item',
            new Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse(
                array(),
                Conjoon_Filter_Input::CONTEXT_RESPONSE
            ),
            false
        );

        $item = $itemDecorator->saveSentEmailAsDto(
            $message, $account, $userId, $mail, $data['type'], $data['referencesId']
        );

        if (!$item) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while saving email';
            $error->message = 'The email was sent, but it could not be stored into the database.';
            $error->level = Conjoon_Error::LEVEL_ERROR;
            $this->view->error   = $error;
            $this->view->success = false;
            $this->view->item    = null;
            return;
        }

        // if the sent email referenced an existing message, tr to fetch this message
        // and send it along as context-referenced item

        $contextReferencedItem = $itemDecorator->getReferencedItemAsDto(
            $item->id,
            $userId
        );


        $this->view->error   = null;
        $this->view->success = true;
        $this->view->item    = $item;
        $this->view->contextReferencedItem  = empty($contextReferencedItem)
                                            ? null
                                            : $contextReferencedItem;
    }

    /**
     * A draft can be loaded from the database if an id was supplied
     * or filled with dummy data if no id was supplied. If no id was supplied,
     * the user wants to create a new email. In this case, the id defaults to
     * -1. If the user requests to save the draft later on, the id will be updated
     * to the value of the auto_increment field of the table.
     * Along with an id the application will need a folder_id so it can tell whether
     * an existing view has to be updated if this draft was edited and the folder
     * is currently visible.
     * Note, that getDraft will also be executed when the user wants to reply to
     * an email or forward an email. in this case, the id defaults to the email to
     * which the user wants to forward/ reply to.
     *
     * The method awaits 4 POST parameters:
     * id - the original message to reply to OR the id of the draft that is being
     * edited
     * type - the context the draft is in: can be either "new", "forward",
     *        "reply", "reply_all" or "edit"
     * name:    the name of an recipient to send this email to
     * address: the address of an recipient to send this email to. If that value is not
     *          empty. id will be set to -1 and type will be set to new. If address equals
     *          to name or if name is left empty, only the address will be used to send the
     *          email to. Address is given presedence in any case
     */
    public function getDraftAction()
    {
        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Draft_Filter_DraftResponse
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft/Filter/DraftResponse.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Account_Model_Account
         */
        require_once 'Conjoon/Modules/Groupware/Email/Account/Model/Account.php';

        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $id   = (int)$_POST['id'];
        $type = (string)$_POST['type'];

        $accountModel = new Conjoon_Modules_Groupware_Email_Account_Model_Account();

        // create a new draft so that the user is able to write an email from scratch!
        if ($id <= 0) {

            /**
             * @see Conjoon_Modules_Groupware_Email_Draft
             */
            require_once 'Conjoon/Modules/Groupware/Email/Draft.php';

            $standardId   = $accountModel->getStandardAccountIdForUser($userId);

            if ($standardId == 0) {
                require_once 'Conjoon/Error.php';
                $error = new Conjoon_Error();
                $error = $error->getDto();;
                $error->title = 'Error while opening draft';
                $error->message = 'Please configure an email account first.';
                $error->level = Conjoon_Error::LEVEL_ERROR;

                $this->view->draft   = null;
                $this->view->success = false;
                $this->view->error   = $error;

                return;
            }

            $post = $_POST;

            Conjoon_Util_Array::apply($post, array(
                'groupwareEmailAccountsId' => $standardId,
                'groupwareEmailFoldersId'  => -1
            ));

            $draftFilter = new Conjoon_Modules_Groupware_Email_Draft_Filter_DraftResponse(
                $post,
                Conjoon_Modules_Groupware_Email_Draft_Filter_DraftResponse::CONTEXT_NEW
            );

            $data = $draftFilter->getProcessedData();

            $draft = Conjoon_BeanContext_Inspector::create(
                'Conjoon_Modules_Groupware_Email_Draft',
                $data
            );

            $this->view->success = true;
            $this->view->error   = null;
            $this->view->draft   = $draft->getDto();
            $this->view->type    = $type;

            return;
        }

        // load an email to edit, to reply or to forward it
        /**
         * @see Conjoon_Modules_Groupware_Email_Draft_Model_Draft
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft/Model/Draft.php';

        $draftModel = new Conjoon_Modules_Groupware_Email_Draft_Model_Draft();
        $draftData = $draftModel->getDraft($id, $userId, $type);

        if (empty($draftData)) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while opening draft';
            $error->message = 'Could not find the referenced draft.';
            $error->level = Conjoon_Error::LEVEL_ERROR;

            $this->view->draft   = null;
            $this->view->success = false;
            $this->view->error   = $error;

            return;
        }

        $context = "";

        switch ($type) {
            case 'reply':
                $context = Conjoon_Modules_Groupware_Email_Draft_Filter_DraftResponse::CONTEXT_REPLY;
            break;

            case 'reply_all':
                $context = Conjoon_Modules_Groupware_Email_Draft_Filter_DraftResponse::CONTEXT_REPLY_ALL;
            break;

            case 'forward':
                $context = Conjoon_Modules_Groupware_Email_Draft_Filter_DraftResponse::CONTEXT_FORWARD;
            break;

            case 'edit':
                $context = Conjoon_Modules_Groupware_Email_Draft_Filter_DraftResponse::CONTEXT_EDIT;
            break;

            default:
                throw new Exception("Type $type not supported.");
            break;
        }

        Conjoon_Util_Array::camelizeKeys($draftData);

        $addresses = $accountModel->getEmailAddressesForUser($userId);

        $draftData['userEmailAddresses'] = $addresses;

        $draftFilter = new Conjoon_Modules_Groupware_Email_Draft_Filter_DraftResponse(
            $draftData,
            $context
        );

        $data = $draftFilter->getProcessedData();

        // convert email addresses
        /**
         * @see Conjoon_Modules_Groupware_Email_Address
         */
        require_once 'Conjoon/Modules/Groupware/Email/Address.php';

        $to   = array();
        $cc   = array();
        $bcc  = array();
        foreach ($data['to'] as $add) {
            $to[] = new Conjoon_Modules_Groupware_Email_Address($add);
        }
        foreach ($data['cc'] as $add) {
            $cc[] = new Conjoon_Modules_Groupware_Email_Address($add);
        }
        foreach ($data['bcc'] as $add) {
            $bcc[] = new Conjoon_Modules_Groupware_Email_Address($add);
        }
        $data['to']  = $to;
        $data['cc']  = $cc;
        $data['bcc'] = $bcc;

        $draft = Conjoon_BeanContext_Inspector::create(
            'Conjoon_Modules_Groupware_Email_Draft',
            $data
        );

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->draft   = $draft->getDto();
        $this->view->type    = $type;
    }

    /**
     * Saves a draft into the database for later editing /sending.
     *
     * Incoming data will be filtered and then saved into the database.
     *
     *
     *
     */
    public function saveDraftAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Draft_Filter_DraftInput
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft/Filter/DraftInput.php';

        $data = array();
        try {
            // the filter will transform the "message" into bodyHtml and bodyText, depending
            // on the passed format. both will only be filled if format equals to "multipart"
            $filter = new Conjoon_Modules_Groupware_Email_Draft_Filter_DraftInput(
                $_POST,
                Conjoon_Modules_Groupware_Email_Draft_Filter_DraftInput::CONTEXT_DRAFT
            );
            $data = $filter->getProcessedData();
        } catch (Exception $e) {
             require_once 'Conjoon/Error.php';
             $error = Conjoon_Error::fromFilter($filter, $e);
             $this->view->success = false;
             $this->view->error   = $error->getDto();
             $this->view->item    = null;
             return;
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Address
         */
        require_once 'Conjoon/Modules/Groupware/Email/Address.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Draft
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft.php';

        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        // create the message object here
        $to  = array();
        $cc  = array();
        $bcc = array();

        $toString  = array();
        $ccString  = array();

        foreach ($data['cc'] as $dcc) {
            $add        = new Conjoon_Modules_Groupware_Email_Address($dcc);
            $cc[]       = $add;
            $toString[] = $add->__toString();
        }
        foreach ($data['bcc'] as $dbcc) {
            $add         = new Conjoon_Modules_Groupware_Email_Address($dbcc);
            $bcc[]       = $add;
        }
        foreach ($data['to'] as $dto) {
            $add        = new Conjoon_Modules_Groupware_Email_Address($dto);
            $to[]       = $add;
            $toString[] = $add->__toString();
        }

        $toString  = implode(', ', $toString);
        $ccString  = implode(', ', $ccString);

        $data['cc']  = $cc;
        $data['to']  = $to;
        $data['bcc'] = $bcc;

        // get the specified account for the user
        require_once 'Conjoon/BeanContext/Decorator.php';
        require_once 'Conjoon/Keys.php';

        $accountDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Account_Model_Account'
        );

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $account = $accountDecorator->getAccountAsEntity($data['groupwareEmailAccountsId'], $userId);

        // no account found?
        if (!$account) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while saving email';
            $error->message = 'Could not find specified account.';
            $error->level = Conjoon_Error::LEVEL_ERROR;
            $this->view->error   = $error;
            $this->view->success = false;
            $this->view->item    = null;
            return;
        }

        $draft = Conjoon_BeanContext_Inspector::create(
                'Conjoon_Modules_Groupware_Email_Draft',
                $data,
                true
        );

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ItemResponse.php';

        $itemDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Item_Model_Item',
            new Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse(
                array(),
                Conjoon_Filter_Input::CONTEXT_RESPONSE
            ),
            false
        );

        $item = $itemDecorator->saveDraftAsDto($draft, $account, $userId, $data['type'], $data['referencesId']);

        if (!$item) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while saving email';
            $error->message = 'The email could not be stored into the database.';
            $error->level = Conjoon_Error::LEVEL_ERROR;
            $this->view->error   = $error;
            $this->view->success = false;
            $this->view->item    = null;
            return;
        }

        $emailRecord = $this->_getEmail($item->id, true);

        $this->view->error       = null;
        $this->view->success     = true;
        $this->view->item        = $item;
        $this->view->emailRecord = $emailRecord;
    }


    /**
     * Saves a draft into the outbox folder of the user.
     */
    public function moveToOutboxAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Draft_Filter_DraftInput
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft/Filter/DraftInput.php';

        $data = array();
        try {
            // the filter will transform the "message" into bodyHtml and bodyText, depending
            // on the passed format. both will only be filled if format equals to "multipart"
            $filter = new Conjoon_Modules_Groupware_Email_Draft_Filter_DraftInput(
                $_POST,
                Conjoon_Filter_Input::CONTEXT_CREATE
            );
            $data = $filter->getProcessedData();
        } catch (Exception $e) {
             require_once 'Conjoon/Error.php';
             $error = Conjoon_Error::fromFilter($filter, $e);
             $this->view->success = false;
             $this->view->error   = $error->getDto();
             $this->view->item    = null;
             return;
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Address
         */
        require_once 'Conjoon/Modules/Groupware/Email/Address.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Draft
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft.php';

        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        // create the message object here
        $to  = array();
        $cc  = array();
        $bcc = array();

        $toString  = array();
        $ccString  = array();

        foreach ($data['cc'] as $dcc) {
            $add        = new Conjoon_Modules_Groupware_Email_Address($dcc);
            $cc[]       = $add;
            $toString[] = $add->__toString();
        }
        foreach ($data['bcc'] as $dbcc) {
            $add         = new Conjoon_Modules_Groupware_Email_Address($dbcc);
            $bcc[]       = $add;
        }
        foreach ($data['to'] as $dto) {
            $add        = new Conjoon_Modules_Groupware_Email_Address($dto);
            $to[]       = $add;
            $toString[] = $add->__toString();
        }

        $toString  = implode(', ', $toString);
        $ccString  = implode(', ', $ccString);

        $data['cc']  = $cc;
        $data['to']  = $to;
        $data['bcc'] = $bcc;

        // get the specified account for the user
        require_once 'Conjoon/BeanContext/Decorator.php';
        require_once 'Conjoon/Keys.php';

        $accountDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Account_Model_Account'
        );

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $account = $accountDecorator->getAccountAsEntity($data['groupwareEmailAccountsId'], $userId);

        // no account found?
        if (!$account) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while moving email to the outbox folder';
            $error->message = 'Could not find specified account.';
            $error->level = Conjoon_Error::LEVEL_ERROR;
            $this->view->error   = $error;
            $this->view->success = false;
            $this->view->item    = null;
            return;
        }

        $draft = Conjoon_BeanContext_Inspector::create(
                'Conjoon_Modules_Groupware_Email_Draft',
                $data,
                true
        );

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ItemResponse.php';

        $itemDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Item_Model_Item',
            new Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse(
                array(),
                Conjoon_Filter_Input::CONTEXT_RESPONSE
            ),
            false
        );

        $item = $itemDecorator->moveDraftToOutboxAsDto($draft, $account, $userId, $data['type'], $data['referencesId']);

        if (!$item) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while saving email';
            $error->message = 'The email could not be stored into the database.';
            $error->level = Conjoon_Error::LEVEL_ERROR;
            $this->view->error   = $error;
            $this->view->success = false;
            $this->view->item    = null;
            return;
        }


        $this->view->error   = null;
        $this->view->success = true;
        $this->view->item    = $item;
    }

    /**
     * Bulk sends emails. Awaits the parameter ids as a numeric array with the ids of
     * the emails which should get send.
     *
     */
    public function bulkSendAction()
    {
        $toSend = $_POST['ids'];

        if ($this->_helper->conjoonContext()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toSend = Zend_Json::decode($toSend, Zend_Json::TYPE_ARRAY);
        }

        $date = null;
        if (isset($_POST['date'])) {
            require_once 'Conjoon/Filter/DateIso8601.php';
            $dateFilter = new Conjoon_Filter_DateIso8601();
            $date = $dateFilter->filter((int)$_POST['date']);
        }

        /**
         * @see Conjoon_Filter_EmailRecipients
         */
        require_once 'Conjoon/Filter/EmailRecipients.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ItemResponse.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Address
         */
        require_once 'Conjoon/Modules/Groupware/Email/Address.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Draft
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft.php';

        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Draft_Model_Draft
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft/Model/Draft.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Draft_Filter_DraftInput
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft/Filter/DraftInput.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Sender
         */
        require_once 'Conjoon/Modules/Groupware/Email/Sender.php';

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $draftFilter = new Conjoon_Modules_Groupware_Email_Draft_Filter_DraftInput(
            array(),
            Conjoon_Filter_Input::CONTEXT_CREATE
        );

        $draftModel = new Conjoon_Modules_Groupware_Email_Draft_Model_Draft();

        $accountDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Account_Model_Account'
        );

        $recipientsFilter = new Conjoon_Filter_EmailRecipients();

        $sendItems              = array();
        $contextReferencedItems = array();

        foreach ($toSend as $id) {

            $id = (int)$id;

            if ($id <= 0) {
                continue;
            }

            $rawDraft = $draftModel->getDraft($id, $userId);

            if (empty($rawDraft)) {
                continue;
            }

            Conjoon_Util_Array::camelizeKeys($rawDraft);


            $account = $accountDecorator->getAccountAsEntity(
                $rawDraft['groupwareEmailAccountsId'],
                $userId
            );

            // no account found?
            if (!$account) {
                /**
                 * @todo think about using the standard account as a fallback or use at last
                 * an error message to inform the user that the account used to write this email
                 * is not available anymore
                 */
                continue;
            }

            $rawDraft['to']  = $recipientsFilter->filter($rawDraft['to']);
            $rawDraft['cc']  = $recipientsFilter->filter($rawDraft['cc']);
            $rawDraft['bcc'] = $recipientsFilter->filter($rawDraft['bcc']);

            // create the message object here
            $to  = array();
            $cc  = array();
            $bcc = array();

            foreach ($rawDraft['cc'] as $dcc) {
                $add        = new Conjoon_Modules_Groupware_Email_Address($dcc);
                $cc[]       = $add;
            }
            foreach ($rawDraft['bcc'] as $dbcc) {
                $add         = new Conjoon_Modules_Groupware_Email_Address($dbcc);
                $bcc[]       = $add;
            }
            foreach ($rawDraft['to'] as $dto) {
                $add        = new Conjoon_Modules_Groupware_Email_Address($dto);
                $to[]       = $add;
            }

            $rawDraft['to']  = $to;
            $rawDraft['cc']  = $cc;
            $rawDraft['bcc'] = $bcc;

            $message = Conjoon_BeanContext_Inspector::create(
                'Conjoon_Modules_Groupware_Email_Draft',
                $rawDraft,
                true
            );

            if ($date !== null) {
                $message->setDate($date);
            }

            try {
                $mail = Conjoon_Modules_Groupware_Email_Sender::send($message, $account);
            } catch (Exception $e) {
                continue;
            }

            // if the email was send successfully, save it into the db and
            // return the params savedId (id of the newly saved email)
            // and savedFolderId (id of the folder where the email was saved in)
            $itemDecorator = new Conjoon_BeanContext_Decorator(
                'Conjoon_Modules_Groupware_Email_Item_Model_Item',
                new Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse(
                    array(),
                    Conjoon_Filter_Input::CONTEXT_RESPONSE
                ),
                false
            );

            $item = $itemDecorator->saveSentEmailAsDto($message, $account, $userId, $mail, '');

            if (!$item) {
                continue;
            }

            $sendItems[] = $item;
            $cri = $itemDecorator->getReferencedItemAsDto($item->id, $userId);
            if (!empty($cri)) {
                $contextReferencedItems[]= $cri;
            }
        }

        $this->view->success   = true;
        $this->view->sentItems = $sendItems;
        $this->view->error     = null;
        $this->view->contextReferencedItems = $contextReferencedItems;

    }
}