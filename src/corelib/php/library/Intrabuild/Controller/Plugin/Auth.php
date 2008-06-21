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
 * Zend_Auth
 */
require_once 'Zend/Auth.php';

/**
 * Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';


/**
 * Zend_Controller_Request_Abstract
 */
require_once 'Zend/Controller/Request/Abstract.php';
 
 /**
  * A plugin that checks if a user is currently logged in before a 
  * dispatch is being made. 
  *
  * @uses Zend_Controller_Plugin_Abstract
  * @package Intrabuild_Controller
  * @subpackage Plugin
  * @category Plugins
  *
  * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
  */  
class Intrabuild_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract {
  
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
     * The method checks for an authenticated user. 
     * 
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // let the user bypass and do not care about authentication state
        /*if ($request->action == 'process' && $request->controller == 'login' &&
            $request->module == 'default') {
            // if the auth is set, redirect to users page
            if ($this->auth->hasIdentity()) {
                $request->setModuleName('page');
                $request->setControllerName('index');
                $request->setActionName('show');
                $request->setParam('id', $this->auth->getStorage()->read()->getId());
            }
             return; 
        }*/
        
        if (!$this->auth->hasIdentity()) {
            $request->setModuleName('default');
            $request->setControllerName('login');
            $request->setActionName('process');
        }
        
    }
}