<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->updated = $result['updated'];
        $this->view->failed  = $result['failed'];
    }

}