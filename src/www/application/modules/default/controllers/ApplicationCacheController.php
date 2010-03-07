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


/**
 * Action controller for sending Application Cache manifest contents to the client.
 *
 * @uses Zend_Controller_Action
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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
     * Returns the view-script with teh contents of the various
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
        $this->_response->setHeader('Content-Type', 'text/cache-manifest', true);

        /**
         * @see Conjoon_Modules_Default_Registry_Facade
         */
        require_once 'Conjoon/Modules/Default/Registry/Facade.php';

        $facade = Conjoon_Modules_Default_Registry_Facade::getInstance();

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

        $fileList = array();
        $userId   = $this->_helper->registryAccess()->getUserId();
        $baseKey  = '/client/applicationCache/';

        if ($appNs->clear !== true) {
            $caches = array(
                'images' => $facade->getValueForKeyAndUserId(
                    $baseKey . 'cache-images', $userId
                ),
                'sounds' => $facade->getValueForKeyAndUserId(
                    $baseKey . 'cache-sounds', $userId
                ),
                'flash' => $facade->getValueForKeyAndUserId(
                    $baseKey . 'cache-flash', $userId
                ),
                'javascript' => $facade->getValueForKeyAndUserId(
                    $baseKey . 'cache-javascript', $userId
                ),
                'html' => $facade->getValueForKeyAndUserId(
                    $baseKey . 'cache-html', $userId
                ),
                'stylesheets' => $facade->getValueForKeyAndUserId(
                    $baseKey . 'cache-stylesheets', $userId
                )
            );

            $applcationPath = $this->_helper->registryAccess()->getApplicationPath();
            $folder         = 'manifest';

            foreach ($caches as $key => $value) {
                if ($value) {
                    $fileList[] = $applcationPath . '/' . $folder . '/' . $key . '.list';
                }
            }
        }

        /**
         * @see Conjoon_Version
         */
        require_once 'Conjoon/Version.php';

        $this->view->conjoonVersion = Conjoon_Version::VERSION;

        $this->view->lastChanged = $facade->getValueForKeyAndUserId(
            $baseKey . 'last-changed', $userId
        );
        $this->view->fileList = $fileList;
    }

    /**
     * Prepares the session to set the "clear" flag in the application
     * namespace based on the passed argument "clear", which can be either
     * "true" or  "false", so that delivering the manifest file while this flag
     * is set to true contains no caching content.
     *
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

        $appNs->clear = $clear;

        $this->view->success = true;
        $this->view->error   = null;
    }

}