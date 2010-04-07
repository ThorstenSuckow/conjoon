<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Groupware_EmailItemController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $this->_helper->filterRequestData()
                      ->registerFilter('Groupware_EmailItemController::download.attachment');

        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('fetch.emails',    self::CONTEXT_JSON)
                       ->addActionContext('move.items',      self::CONTEXT_JSON)
                       ->addActionContext('delete.items',    self::CONTEXT_JSON)
                       ->addActionContext('get.email.items', self::CONTEXT_JSON)
                       ->addActionContext('get.email',       self::CONTEXT_JSON)
                       ->addActionContext('set.email.flag',  self::CONTEXT_JSON)
                       ->initContext();
    }

    /**
     * Sets header to the mime type of the attachment as queried from the
     * database and tries to send the file contents to the client.
     * To identify the attachment, the action needs the parameters "key"
     * and "id" as found in the data model of the attachments.
     *
     */
    public function downloadAttachmentAction()
    {
        $attachmentId  = $this->_request->getParam('id');
        $attachmentKey = $this->_request->getParam('key');
        $userId        = $this->_helper->registryAccess->getUserId();

        $downloadCookieName = $this->_request->getParam('downloadCookieName');

        /**
         * @see Conjoon_Modules_Groupware_Email_Attachment_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Attachment/Facade.php';

        $facade = Conjoon_Modules_Groupware_Email_Attachment_Facade::getInstance();

        $data = $facade->getAttachmentDownloadDataForUserId(
            $attachmentKey, $attachmentId, $userId
        );

        if (!$data) {
            /**
             * @see Conjoon_Exception
             */
            require_once 'Conjoon/Exception.php';

            // we'll throw an exception, that's okay for now
            throw new Conjoon_Exception("Sorry, but the requested attachment is not available.");

            return;
        }

        $this->_helper->viewRenderer->setNoRender();


        $response = $this->getResponse();
        $response->clearAllHeaders();

        setcookie($downloadCookieName, 'downloading', 0,  '/');

        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                 ->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true)
                 ->setHeader('Pragma', 'no-cache', true)
                 ->setHeader('Content-Description', $data['fileName'], true)
                 ->setHeader('Content-Type', $data['mimeType'], true)
                 ->setHeader('Content-Transfer-Encoding', 'binary', true)
                 ->setHeader(
                    'Content-Disposition',
                    'attachment; filename="'.addslashes($data['fileName']).'"',
                    true
                 );

        $response->sendHeaders();
        $response->setBody($data['content']);
    }

    /**
     * Queriesan email account for new wmails. If an account id was posted, only the
     * specified account will be queried.
     * Sends all newly fetched emails back to the client.
     *
     */
    public function fetchEmailsAction()
    {
        /*@REMOVE@*/
        if (!$this->_helper->connectionCheck()) {
            $this->view->success    = true;
            $this->view->totalCount = 0;
            $this->view->items      = array();
            $this->view->error      = null;

            return;
        }
        /*@REMOVE@*/

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
        /**
         * @see Conjoon_Modules_Groupware_Email_Message_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Message/Facade.php';

        $message = Conjoon_Modules_Groupware_Email_Message_Facade::getInstance()
                   ->getMessage(
                        (int)$_POST['id'],
                        $this->_helper->registryAccess()->getUserId()
                   );

        if (!$message) {
            $this->view->success = true;
            $this->view->error   = null;
            $this->view->item    = null;
            return;
        }

        $this->view->success     = true;
        $this->view->error       = null;
        $this->view->item        = $message;
    }




}