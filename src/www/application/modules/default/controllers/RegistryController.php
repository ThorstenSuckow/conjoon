<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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

class RegistryController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('get.entries', self::CONTEXT_JSON)
                       ->initContext();
    }

    /**
     * Responds with a list of registry entries that are valid
     * for an installation of this software.
     *
     *
     */
    public function getEntriesAction()
    {
        $userId = $this->_helper->registryAccess()->getUserId();

        /**
         * @see Conjoon_Modules_Default_Registry_Facade
         */
        require_once 'Conjoon/Modules/Default/Registry/Facade.php';

        $registry = Conjoon_Modules_Default_Registry_Facade::getInstance()
                    ->getRegistryForUserId($userId);

        $this->view->entries = $registry;
        $this->view->success = true;
        $this->view->error   = null;
    }

}