<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Auth.php 19 2008-07-13 20:39:45Z T. Suckow $
 * $Date: 2008-07-13 22:39:45 +0200 (So, 13 Jul 2008) $
 * $Revision: 19 $
 * $LastChangedDate: 2008-07-13 22:39:45 +0200 (So, 13 Jul 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Controller/Plugin/Auth.php $
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
  * @package Intrabuild_Controller
  * @subpackage Plugin
  * @category Plugins
  *
  * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
  */
class Intrabuild_Controller_Plugin_Lock extends Zend_Controller_Plugin_Abstract {


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
        require_once 'Intrabuild/Keys.php';

        $receptionControllerNs = new Zend_Session_Namespace(
            Intrabuild_Keys::SESSION_CONTROLLER_RECEPTION
        );

        $isLocked = $receptionControllerNs->locked;

        if ($isLocked === true) {
            // the following requests may still be processed when a user's
            // session is logged
            if ($request->controller == 'reception' && $request->module == 'default') {
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