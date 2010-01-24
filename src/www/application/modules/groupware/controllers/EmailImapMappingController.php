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
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Groupware_EmailImapMappingController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('get.mappings',    self::CONTEXT_JSON)
                       ->initContext();
    }

    /**
     * Returns a list of currently mapped IMAP folders/mailboxes to
     * specific types to the client.
     *
     */
    public function getMappingsAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_ImapMapping_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapMapping/Facade.php';

        $facade = Conjoon_Modules_Groupware_Email_ImapMapping_Facade::getInstance();

        $userId = $this->_helper->registryAccess()->getUserId();

        $mappings = $facade->getImapMappingsForUserId($userId);

        $this->view->success  = true;
        $this->view->error    = null;
        $this->view->mappings = $mappings;
    }

}