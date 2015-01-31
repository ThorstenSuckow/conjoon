<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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

/**
 * Multi-purpose error controller.
 * The errorAction will be called whenever an exception was throwsn
 * in any action and not trapped.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ErrorController extends Zend_Controller_Action {

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('error', 'json')
                       ->addActionContext('error', 'jsonHtml')
                       ->initContext();
    }

    /**
     * Automatically called by the controller instance whenever
     * an exception was thrown and not trapped in a catch clause.
     * Since this controller supports conjoonContext, the format
     * of the values assigned to the view-variables may differ depending
     * on the format used (e.g. json encoded sting for context 'json').
     */
    public function errorAction()
    {
        /**
         * @see Conjoon_Controller_DispatchHelper
         */
        require_once 'Conjoon/Controller/DispatchHelper.php';

        $exception = $this->_getParam('error_handler')->exception;
        $result = Conjoon_Controller_DispatchHelper::transformExceptions(array($exception));

        $this->getResponse()->clearBody();

        $userId = $this->_helper->registryAccess()->getUserId();

        /**
         * @see Conjoon_Modules_Default_Registry_Facade
         */
        require_once 'Conjoon/Modules/Default/Registry/Facade.php';

        $this->view->title = Conjoon_Modules_Default_Registry_Facade::getInstance()
                             ->getValueForKeyAndUserId('/base/conjoon/name', $userId);

        foreach ($result as $key => $value) {
            $this->view->{$key} = $value;
        }
    }

}