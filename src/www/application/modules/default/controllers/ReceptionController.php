<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: LoginController.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/www/application/modules/default/controllers/LoginController.php $
 */

/**
 * Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';


/**
 * Action controller for login/logout.
 *
 * @uses Zend_Controller_Action
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class ReceptionController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $contextSwitch = $this->_helper->contextSwitch();

        $contextSwitch->addActionContext('logout',   self::CONTEXT_JSON)
                      ->addActionContext('process',  self::CONTEXT_JSON)
                      ->addActionContext('login',    self::CONTEXT_JSON)
                      ->addActionContext('index',    self::CONTEXT_JSON)
                      ->addActionContext('ping',     self::CONTEXT_JSON)
                      ->addActionContext('lock',     self::CONTEXT_JSON)
                      ->addActionContext('unlock',   self::CONTEXT_JSON)
                      ->addActionContext('get.user', self::CONTEXT_JSON)
                      ->initContext();
    }

    /**
     * Displays the information about the currently logged in user.
     *
     */
    public function getUserAction()
    {
        require_once 'Zend/Registry.php';
        require_once 'Intrabuild/Keys.php';

        $auth = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $user = $auth->getIdentity();

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->user    = $user->getDto();
    }

    /**
     * Action for a request that the session for the user sould be locked.
     * Locking the session means that no further requests are being processed
     * (except for the actions ping/logout/login/unlock requests as defined by
     * this controller) until the session is unlocked.
     *
     * @see unlockAction
     */
    public function lockAction()
    {
        require_once 'Zend/Session/Namespace.php';
        require_once 'Intrabuild/Keys.php';

        $receptionControllerNs = new Zend_Session_Namespace(
            Intrabuild_Keys::SESSION_CONTROLLER_RECEPTION
        );

        $receptionControllerNs->locked = true;

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->locked  = true;
    }

    /**
     * Action for a request that the session for the user sould be unlocked.
     * It is important to check if the submitted user credentials match the
     * user information stored in the auth object, so that no user other account
     * can hijack the current active session!
     */
    public function unlockAction()
    {
        require_once 'Zend/Session/Namespace.php';
        require_once 'Intrabuild/Keys.php';
        require_once 'Zend/Registry.php';
        require_once 'Intrabuild/BeanContext/Decorator.php';

        $auth = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $id = $auth->getIdentity()->getId();

        /**
         * @todo Filter username and password!
         */
        $username = $this->_getParam('username');
        $password = $this->_getParam('password');

        $decorator = new Intrabuild_BeanContext_Decorator(
            'Intrabuild_Modules_Default_User_Model_User'
        );

        $user = $decorator->getUserForEmailCredentialsAsEntity(
            $username,
            md5($password)
        );

        if ($user === null || $user->getId() !== $id) {
            $this->view->error = "The user credentials you provided did not "
                               . "match the credentials of the currently logged "
                               . "in user.";
            return;
        }


        $receptionControllerNs = new Zend_Session_Namespace(
            Intrabuild_Keys::SESSION_CONTROLLER_RECEPTION
        );

        $receptionControllerNs->locked = false;

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->locked  = false;
    }

    /**
     * Called in a frequent interval when the AJAX driven application needs
     * to keep the users session alive,
     *
     */
    public function pingAction()
    {
        $this->view->success = true;
        $this->view->error   = null;
    }

    /**
     * Index action of the controller.
     * This action will be called whenever the application recognizes that the
     * user is not logged in anymore. This can be upon start, when the user has
     * to log in, or in another context, when the user uses the application and
     * his session gets lost.
     * Additionaly, this action will be called when the application detects that
     * the session of the user had been locked.
     * In all cases, the status code "401" will be send to
     * indicate that authorization is required.
     *
     * @see Intrabuild_Controller_Plugin_Auth
     */
    public function indexAction()
    {
        $this->_response->setHttpResponseCode(401);

        require_once 'Zend/Session/Namespace.php';
        require_once 'Intrabuild/Keys.php';

        $receptionControllerNs = new Zend_Session_Namespace(
            Intrabuild_Keys::SESSION_CONTROLLER_RECEPTION
        );

        $isLocked = $receptionControllerNs->locked;

        require_once 'Intrabuild/Error.php';
        $error = new Intrabuild_Error();

        $error->setCode(-1);
        $error->setLevel(Intrabuild_Error::LEVEL_ERROR);
        $error->setFile(__FILE__);
        $error->setLine(__LINE__);

        if ($isLocked === true) {
            $error->setMessage("Workbench is locked. You need to log in again to access this resource.");
            $error->setType(Intrabuild_Error::LOCKED);
            $this->view->locked = true;
        } else {
            $error->setMessage("Authorization required. You need to log in to access this resource.");
            $error->setType(Intrabuild_Error::AUTHORIZATION);
            $this->view->authorized = false;
        }

        /**
         * @see Intrabuild_Modules_Default_Registry
         */
        require_once 'Intrabuild/Modules/Default/Registry.php';

        $this->view->title = Intrabuild_Modules_Default_Registry::get(
            '/base/conjoon/name'
        );

        $this->view->success    = false;
        $this->view->error      = $error->getDto();

    }

    /**
     * Logout action of the controller.
     * Logs a user completely out of the application.
     *
     */
    public function logoutAction()
    {
        Zend_Session::destroy(true, true);

        $this->view->success = true;
        $this->view->error   = null;
    }

    public function processAction()
    {
        require_once 'Intrabuild/Auth/Adapter/Db.php';

        /**
         * @todo Filter username and password!
         */
        $username = $this->_getParam('username');
        $password = $this->_getParam('password');

        $auth        = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $authAdapter = new Intrabuild_Auth_Adapter_Db($username, $password);

        // if the result is valid, the return value of the adapter will
        // be stored automatically in the supplied storage object
        // from the auth object
        $result = $auth->authenticate($authAdapter);

        if ($result->isValid()) {
            $this->view->success = true;
       } else {
            $this->view->error   = $result->getMessages();
            $this->view->success = false;
        }
    }

}