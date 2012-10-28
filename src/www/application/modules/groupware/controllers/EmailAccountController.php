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
 * Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Groupware_EmailAccountController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('add.email.account',     self::CONTEXT_JSON)
                       ->addActionContext('get.email.accounts',    self::CONTEXT_JSON)
                       ->addActionContext('update.email.accounts', self::CONTEXT_JSON)
                       ->initContext();
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
     * <li>inbox_connection_type: Secure connection type for the incoming
     * mail server. Empty for unsecure connection, or SSL or TLS</li>
     * <li>outbox_connection_type: Secure connection type for the outgoing mail
     * server. Empty for unsecure connection, or SSL or TLS. If <tt>outbox_auth</tt>
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
     *  <li>rootFolder: The root folder that saves the tree hierarchy for this
     * account.</li>
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

        /**
         * @see Conjoon_Builder_Factory
         */
        require_once 'Conjoon/Builder/Factory.php';

        // clean cache in any case
        Conjoon_Builder_Factory::getBuilder(
            Conjoon_Keys::CACHE_EMAIL_ACCOUNTS,
            Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
        )->cleanCacheForTags(array('userId' => $userId));

        $classToCreate = 'Conjoon_Modules_Groupware_Email_Account';

        $this->view->success = true;
        $this->view->error   = null;

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

            // read out root folder for account
            require_once 'Conjoon/BeanContext/Decorator.php';
            $decoratedFolderModel = new Conjoon_BeanContext_Decorator(
                'Conjoon_Modules_Groupware_Email_Folder_Model_Folder',
                null, false
            );
            $rootId = $decoratedFolderModel->getAccountsRootOrRootFolderId($addedId, $userId);

            if ($rootId != 0) {
                $this->view->rootFolder = $decoratedFolderModel->getFolderBaseDataAsDto($rootId);
            }

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
        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $user = Zend_Registry::get(
            Conjoon_Keys::REGISTRY_AUTH_OBJECT
        )->getIdentity();

        $userId = $user->getId();

        /**
         * @see Conjoon_Builder_Factory
         */
        require_once 'Conjoon/Builder/Factory.php';

        $data = Conjoon_Builder_Factory::getBuilder(
            Conjoon_Keys::CACHE_EMAIL_ACCOUNTS,
            Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
        )->get(array('userId' => $userId));

        $this->view->success  = true;
        $this->view->accounts = $data;
        $this->view->error    = null;
    }

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

        $numToUpdate = count($toUpdate);
        $numToDelete = count($toDelete);

        if ($numToUpdate != 0 || $numToDelete != 0) {
            /**
             * @see Conjoon_Builder_Factory
             */
            require_once 'Conjoon/Builder/Factory.php';

            Conjoon_Builder_Factory::getBuilder(
                Conjoon_Keys::CACHE_EMAIL_ACCOUNTS,
                Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
            )->cleanCacheForTags(array(
                'userId' => Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT)
                            ->getIdentity()->getId()
            ));
        }

        for ($i = 0; $i < $numToDelete; $i++) {
            $affected = $model->deleteAccount(
                $toDelete[$i], $this->_helper->registryAccess()->getUserId()
            );
            if ($affected == 0) {
                $deletedFailed[] = $toDelete[$i];
            }
        }

        for ($i = 0; $i < $numToUpdate; $i++) {
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

}