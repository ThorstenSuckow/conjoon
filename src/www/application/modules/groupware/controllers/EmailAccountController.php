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
class Groupware_EmailAccountController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('add.email.account',     self::CONTEXT_JSON)
                       ->addActionContext('get.email.accounts',    self::CONTEXT_JSON)
                       ->addActionContext('update.email.accounts', self::CONTEXT_JSON)
                       ->initContext();
    }

    /**
     * The demo will not store any new email accounts.
     */
    public function addEmailAccountAction()
    {
        require_once 'Conjoon/Error.php';
        require_once 'Conjoon/Error/Factory.php';

        $error = Conjoon_Error_Factory::createError(
            $message = "Sorry, but the conjoon demo does not allow for adding new email accounts.",
            Conjoon_Error::LEVEL_WARNING,
            Conjoon_Error::UNKNOWN,
            /*$code =*/ null,
            /*$file =*/ null,
            /*$line =*/ null
        );

        $this->view->account = array();
        $this->view->success = false;
        $this->view->error   = $error->getDto();
    }

    /**
     * Reads out all email accounts belonging to the currently logged in user and
     * assigns them to the view variables, using the appropriate dto.
     * The format will differ from the actual context the action was requested
     * (e.g. context json will assign json encoded strings to the view variables).
     *
     * Passwords set for this account will be masked with "*".
     */
    public function getEmailAccountsAction()
    {
        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $user = Zend_Registry::get(
            Conjoon_Keys::REGISTRY_AUTH_OBJECT
        )->getIdentity();

        $userId = $user->getId();

        /**
         * @see Conjoon_Builder_Factory
         */
        require_once 'Conjoon/Builder/Factory.php';

        $data = Conjoon_Builder_Factory::getBuilder(
            Conjoon_Keys::CACHE_EMAIL_ACCOUNTS,
            Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
        )->get(array('userId' => $userId));

        $this->view->success  = true;
        $this->view->accounts = $data;
        $this->view->error    = null;
    }

    /**
     * The demo does not allow for editing/updating/removing email accounts.
     */
    public function updateEmailAccountsAction()
    {
        require_once 'Conjoon/Error.php';
        require_once 'Conjoon/Error/Factory.php';

        $error = Conjoon_Error_Factory::createError(
            $message = "Sorry, but the conjoon demo does not allow for editing/removing email accounts",
            Conjoon_Error::LEVEL_WARNING,
            Conjoon_Error::UNKNOWN,
            /*$code =*/ null,
            /*$file =*/ null,
            /*$line =*/ null
        );

        $updatedFailed = array();
        $deletedFailed = array();

        if ($this->_helper->conjoonContext()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toDelete = Zend_Json::decode($_POST['deleted'], Zend_Json::TYPE_ARRAY);
            $toUpdate = Zend_Json::decode($_POST['updated'], Zend_Json::TYPE_ARRAY);

            for ($i = 0, $len = count($toDelete); $i < $len; $i++) {
                $deletedFailed[] = $toDelete[$i];
            }

            for ($i = 0, $len = count($toUpdate); $i < $len; $i++) {
                $updatedFailed[] = $toUpdate[$i]['id'];
            }

        }

        $this->view->success       = false;
        $this->view->error         = $error->getDto();
        $this->view->updatedFailed = $updatedFailed;
        $this->view->deletedFailed = $deletedFailed;

    }

}