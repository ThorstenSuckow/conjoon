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
 * @see Zend_Auth
 */
require_once 'Zend/Auth.php';

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

 /**
  * A plugin that checks if a user is currently logged in before a
  * dispatch is being made.
  *
  * If the user is not logged in, the plugin will alter teh request to
  * redirect to the login process. The plugin does also take the current
  * format of the request being made into account.
  *
  * @uses Zend_Controller_Plugin_Abstract
  * @package Conjoon_Controller
  * @subpackage Plugin
  * @category Plugins
  *
  * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
  */
class Conjoon_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract {

    /**
     * @var Zend_Auth
     */
    private $auth;

    /**
     * Constructor.
     *
     * @param Zend_Auth $auth The authentication-object to use for
     * authentication checks
     */
    public function __construct(Zend_Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Called before an action is dispatched.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * The method checks for an authenticated user. It does also compare the
     * authToken property of teh user with the auth_token field in the db - if the
     * authToken is set in the db and does not equal to the authToken in the session,
     * then it is assumed that another user has signed in with the same credentials, and
     * the user's current session will be invalidated.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // check here if the user's authentity is already set
        if ($this->auth->hasIdentity()) {

            // identity is set. Now check for auth token equality
            $currentUser = $this->auth->getIdentity();

            /**
             * @see Conjoon_BeanContext_Decorator
             */
            require_once 'Conjoon/BeanContext/Decorator.php';

            /**
             * @see Conjoon_Modules_Default_User_Model_User
             */
            require_once 'Conjoon/Modules/Default/User/Model/User.php';

            $decorator   = new Conjoon_BeanContext_Decorator(new Conjoon_Modules_Default_User_Model_User());
            $tokenedUser = $decorator->getUserAsDto($currentUser->getId());

            // check whether the token in the DB equals to the token in the session
            if ($tokenedUser->authToken != $currentUser->getAuthToken()) {

                // the application needs to query the registry. That's okay since no secret data will
                // be transported if the registry sees that there's no login
                if ($request->action == 'get.entries' && $request->controller == 'registry' &&
                    $request->module == 'default') {
                    return;
                }

                // user wants to log out - this is needed to sign in again since the
                // active session will prevent from continue with using the app
                if ($request->action == 'logout' && $request->controller == 'reception' &&
                    $request->module == 'default') {
                    return;
                }

                // does not equal - someone has logged in currently
                // with the same user credentials.
                // redirect to appropriate controller action
                $request->setModuleName('default');
                $request->setControllerName('reception');
                $request->setActionName('auth.token.failure');
            }

            return;
        }

        // the user wants to login and requested the login controller's process
        // action. Let him pass!
        if ($request->action == 'process' && $request->controller == 'reception' &&
            $request->module == 'default') {
            return;
        }

        // the application needs to query the registry. That's okay since no secret data will
        // be transported if the registry sees that there's no login
        if ($request->action == 'get.entries' && $request->controller == 'registry' &&
            $request->module == 'default') {
            return;
        }

        // anything other means the user is not logged in
        $request->setModuleName('default');
        $request->setControllerName('reception');
        $request->setActionName('index');
   }
}