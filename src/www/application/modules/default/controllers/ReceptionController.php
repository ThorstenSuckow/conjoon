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

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $contextSwitch = $this->_helper->contextSwitch();

        $contextSwitch->addActionContext('logout', 'json')
                      ->initContext();
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


}