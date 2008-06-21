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
 * Multi-purpose error controller.
 * The errorAction will be called whenever an exception was throwsn
 * in any action and not trapped.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */    
class ErrorController extends Zend_Controller_Action {    

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()    
    {   
        $contextSwitch = $this->_helper->contextSwitch();
        
        $contextSwitch->addActionContext('error', 'json')             
                      ->initContext();    
    } 

    /**
     * Automatically called by the controller instance whenever
     * an exception was thrown and not trapped in a catch clause.
     * Since this controller supports contextSwitch, the format
     * of the values assigned to the view-variables may differ depending
     * on the format used (e.g. json encoded sting for context 'json').
     */
    public function errorAction()    
    {    
        require_once 'Intrabuild/BeanContext/Inspector.php';         
        require_once 'Intrabuild/Error.php';         
        
        $errors = $this->_getParam('error_handler');        
        
        $error = array();
        
        echo "<pre>";
        var_dump($errors->exception);
        
        switch ($errors->type) {            
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                $error = Intrabuild_Error::fromException($errors->exception);
            break;
        }    
                  
        $this->getResponse()->clearBody();        
        $this->view->success = false;    
        $this->view->error   = $error->getDto();    
    }

}
?>