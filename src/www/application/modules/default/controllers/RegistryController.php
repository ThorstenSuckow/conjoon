<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
     * @todo move to model, db, access user using registry
     */
    public function getEntriesAction()
    {
        /**
         * @see Conjoon_Modules_Default_Registry
         */
        require_once 'Conjoon/Modules/Default/Registry.php';

        $this->view->entries = array(
            array(
                'key'   => '/client/system/sfx/enabled',
                'value' => Conjoon_Modules_Default_Registry::get(
                    '/client/system/sfx/enabled'
                )
            ),
            array(
                'key'   => '/client/environment/device',
                'value' => Conjoon_Modules_Default_Registry::get(
                    '/client/environment/device'
                )
            ),
            array(
                'key'   => '/server/php/max_execution_time',
                'value' => Conjoon_Modules_Default_Registry::get(
                    '/server/php/max_execution_time'
                )
            ),
            array(
                'key'   => '/server/environment/host',
                'value' => Conjoon_Modules_Default_Registry::get(
                    '/server/environment/host'
                )
            ),
            array(
                'key'   => '/server/environment/protocol',
                'value' => Conjoon_Modules_Default_Registry::get(
                    '/server/environment/protocol'
                )
            ),
            array(
                'key'   => '/server/environment/port',
                'value' => Conjoon_Modules_Default_Registry::get(
                    '/server/environment/port'
                )
            ),
            array(
                'key'   => '/base/conjoon/name',
                'value' => Conjoon_Modules_Default_Registry::get(
                    '/base/conjoon/name'
                )
            ),
            array(
                'key'   => '/base/conjoon/version',
                'value' => Conjoon_Modules_Default_Registry::get(
                    '/base/conjoon/version'
                )
            ),
            array(
                'key'   => '/base/conjoon/edition',
                'value' => Conjoon_Modules_Default_Registry::get(
                    '/base/conjoon/edition'
                )
            )
        );

        $this->view->success = true;
        $this->view->error   = null;
    }

}