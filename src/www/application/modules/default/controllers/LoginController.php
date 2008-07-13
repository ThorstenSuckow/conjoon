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
 * Action controller for login. 
 * This controller provides context-switch functionality to deliver
 * data in different formats to the client.
 *
 * @uses Zend_Controller_Action
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */    
class LoginController extends Zend_Controller_Action {    

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()    
    {   
        $contextSwitch = $this->_helper->contextSwitch();
        
        $contextSwitch->addActionContext('process', 'json')             
                      ->initContext();    
    } 

    /**
     * Index action of the controller.
     * Shows the login screen.
     *
     */
    public function indexAction()    
    {   
        
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

         

         