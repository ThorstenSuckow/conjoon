<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: IndexController.php 312 2008-11-28 21:59:26Z T. Suckow $
 * $Date: 2008-11-28 22:59:26 +0100 (Fr, 28 Nov 2008) $
 * $Revision: 312 $
 * $LastChangedDate: 2008-11-28 22:59:26 +0100 (Fr, 28 Nov 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild_rep/trunk/src/www/application/modules/default/controllers/IndexController.php $
 */

/**
 * Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

class RegistryController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $contextSwitch = $this->_helper->contextSwitch();

        $contextSwitch->addActionContext('get.entries', self::CONTEXT_JSON)
                      ->initContext();
    }

    /**
     * Responds with a list of registry entries that are valid
     * for an installation of this software.
     *
     * @todo move to model, db, access user using registry
     */
    public function getEntriesAction()
    {
        /**
         * @see Intrabuild_Modules_Default_Registry
         */
        require_once 'Intrabuild/Modules/Default/Registry.php';

        $this->view->entries = array(
            array(
                'key'   => '/base/conjoon/name',
                'value' => Intrabuild_Modules_Default_Registry::get(
                    '/base/conjoon/name'
                )
            ),
            array(
                'key'   => '/base/conjoon/version',
                'value' => Intrabuild_Modules_Default_Registry::get(
                    '/base/conjoon/version'
                )
            ),
            array(
                'key'   => '/base/conjoon/edition',
                'value' => Intrabuild_Modules_Default_Registry::get(
                    '/base/conjoon/edition'
                )
            )
        );

        $this->view->success = true;
        $this->view->error   = null;
    }

}