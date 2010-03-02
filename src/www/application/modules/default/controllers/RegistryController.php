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
                       ->addActionContext('set.entries', self::CONTEXT_JSON)
                       ->initContext();

        $this->_helper->filterRequestData()
                      ->registerFilter('RegistryController::set.entries');
    }

    /**
     * Responds with a list of registry entries that are valid
     * for an installation of this software.
     *
     *
     */
    public function getEntriesAction()
    {
        /**
         * @see Conjoon_Modules_Default_Registry_Facade
         */
        require_once 'Conjoon/Modules/Default/Registry/Facade.php';

        $registry = Conjoon_Modules_Default_Registry_Facade::getInstance()
                    ->getRegistryForUserId(
                        $this->_helper->registryAccess()->getUserId()
                    );

        $this->view->entries = $registry;
        $this->view->success = true;
        $this->view->error   = null;
    }

    /**
     * Expects a list of key/value pairs that should get saved
     * on the server.
     * Responds with the following properties:
     * failed - an array holding keys that were not
     * updated - an array with keys that were actually
     *
     */
    public function setEntriesAction()
    {
        /**
         * @see Conjoon_Modules_Default_Registry_Facade
         */
        require_once 'Conjoon/Modules/Default/Registry/Facade.php';

        $facade = Conjoon_Modules_Default_Registry_Facade::getInstance();

        $data   = $this->_request->getParam('data');
        $userId = $this->_helper->registryAccess()->getUserId();

        $result = $facade->setEntriesFromDataForUserId($data, $userId);

        var_dump($result);


        require_once 'Conjoon/Error/Factory.php';

        $this->view->success = true;
        $this->view->error   = null;/*Conjoon_Error_Factory::createError(
            "Error"
        )->getDto();*/
        $this->view->updated = array();
        $this->view->failed  = array();
    }

}