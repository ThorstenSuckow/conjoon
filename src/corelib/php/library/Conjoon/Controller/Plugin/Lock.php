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
 * Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

 /**
  * A plugin that checks if the session of a user is currently locked.
  *
  * If the session is lcoked, the current request is being denied and instead
  * the index-action of the reception controller is being processed.
  *
  * @uses Zend_Controller_Plugin_Abstract
  * @package Conjoon_Controller
  * @subpackage Plugin
  * @category Plugins
  *
  * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
  */
class Conjoon_Controller_Plugin_Lock extends Zend_Controller_Plugin_Abstract {


    /**
     * Called before an action is dispatched.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * The method checks for an authenticated user.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        require_once 'Zend/Session/Namespace.php';
        require_once 'Conjoon/Keys.php';

        $receptionControllerNs = new Zend_Session_Namespace(
            Conjoon_Keys::SESSION_CONTROLLER_RECEPTION
        );

        $isLocked = $receptionControllerNs->locked;

        if ($isLocked === true) {
            // the following requests may still be processed when a user's
            // session is locked
            if ($request->controller == 'registry' && $request->module == 'default') {
                switch ($request->action) {
                    // we need the registry, at least the basic entries
                    case 'get.entries':
                    return;
                }
            } else if ($request->controller == 'reception' && $request->module == 'default') {
                switch ($request->action) {
                    // the user wants to unlock the session. Give him a try!
                    case 'unlock':
                    // another request for locking the session may still be made!
                    case 'lock':
                    // someone requests a fresh relogin, so let him logout the
                    // locked session first!
                    case 'logout':
                    // the frontend needs to know who's the user which session
                    // got locked!
                    case 'get.user':
                    // the frontend needs to keep the user's session alive!
                    case 'ping':
                    // auth token failure? let him pass
                    case 'auth.token.failure':
                    return;
                }
            }

            // deny access and route to index action of reception
            $request->setModuleName('default');
            $request->setControllerName('reception');
            $request->setActionName('index');
        }
   }
}