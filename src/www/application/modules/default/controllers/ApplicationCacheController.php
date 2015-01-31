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
 * Action controller for sending Application Cache manifest contents to the
 * client.
 *
 * @uses Zend_Controller_Action
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ApplicationCacheController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext(
            'set.clear.flag', self::CONTEXT_JSON
        )->initContext();

        $this->_helper->filterRequestData()
                      ->registerFilter('ApplicationCacheController::set.clear.flag', true);
    }

    /**
     * Returns the view-script with the contents of the various
     * manifest/*.list files based on the localCache settings as found in the
     * registry.
     * The Content-type header for the generated response is explicitely set to
     * "text/cache-manifest".
     * The action will also look up the "clear" flg in the appCache session
     * namespace. If this flag is true, an empty manifest file will be returned,
     * which should force the client's browser to invalidate its cache.
     *
     *
     */
    public function getManifestAction()
    {
        /**
         * Override cache-control header by hand, or else Firefox does not
         * update the manifest somehow, even when setting cache-control to
         * "no-cache"
         * @ticket CN-857
         */
        $this->_response->setHeader("Cache-Control", "", true);

        $this->_response->setHeader(
            'Content-Type', 'text/cache-manifest', true
        );

        /**
         * @see Zend_Session_Namespace
         */
        require_once 'Zend/Session/Namespace.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $appNs = new Zend_Session_Namespace(
            Conjoon_Keys::SESSION_APPLICATION_CACHE
        );

        $files  = array();
        $userId = $this->_helper->registryAccess()->getUserId();

        /**
         * @see Conjoon_Modules_Default_ApplicationCache_Facade
         */
        require_once 'Conjoon/Modules/Default/ApplicationCache/Facade.php';

        $appCacheFacade = Conjoon_Modules_Default_ApplicationCache_Facade
                          ::getInstance();

        if (!$appNs->clear) {

            $applicationPath = $this->_helper->registryAccess()->getApplicationPath();

            $files = $appCacheFacade->getManifestFileListForUserId(
                $userId, $applicationPath . '/manifest'
            );
        }

        /**
         * @see Conjoon_Version
         */
        require_once 'Conjoon/Version.php';

        $this->view->conjoonVersion = Conjoon_Version::VERSION;
        $this->view->lastChanged    = $appCacheFacade
                                      ->getCacheLastChangedTimestampForUserId(
                                          $userId);
        $this->view->files = $files;
    }

    /**
     * Prepares the session to set the "clear" flag in the application
     * namespace based on the passed argument "clear", which can be either
     * "true" or  "false", so that delivering the manifest file while this flag
     * is set to true contains no caching content.
     * If the flag is set to "false", this method will also assign the total
     * number of cache entries to the view-variable "cacheEntryCount".
     * If the flag is set to true, "cacheEntryCount" will be set to "0".
     */
    public function setClearFlagAction()
    {
        $clear = $this->_request->getParam('clear');


        /**
         * @see Zend_Session_Namespace
         */
        require_once 'Zend/Session/Namespace.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $appNs = new Zend_Session_Namespace(
            Conjoon_Keys::SESSION_APPLICATION_CACHE
        );

        $appNs->clear    = $clear;
        $cacheEntryCount = 0;
        $userId          = $this->_helper->registryAccess()->getUserId();

        /**
         * @see Conjoon_Modules_Default_ApplicationCache_Facade
         */
        require_once 'Conjoon/Modules/Default/ApplicationCache/Facade.php';

        $appCacheFacade = Conjoon_Modules_Default_ApplicationCache_Facade
                          ::getInstance();

        $appCacheFacade->setCacheLastChangedTimestampForUserId(
            microtime(true), $userId
        );

        if (!$clear) {
            /**
             * @see Conjoon_Modules_Default_ApplicationCache_Facade
             */
            require_once 'Conjoon/Modules/Default/ApplicationCache/Facade.php';

            $applicationPath = $this->_helper->registryAccess()
                               ->getApplicationPath();

            $cacheEntryCount = $appCacheFacade->getCacheEntryCountForUserId(
                $userId, $applicationPath . '/manifest'
            );
        }

        $this->view->success         = true;
        $this->view->cacheEntryCount = $cacheEntryCount;
        $this->view->error           = null;
    }

}