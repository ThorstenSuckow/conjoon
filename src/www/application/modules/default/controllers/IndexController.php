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

class IndexController extends Zend_Controller_Action {

    const CONTEXT_IPHONE = 'iphone';

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();
        $conjoonContext->addActionContext('index', self::CONTEXT_IPHONE)
                       ->initContext();
    }

    public function indexAction()
    {
        /**
         * @see Conjoon_Modules_Default_Registry
         */
        require_once 'Conjoon/Modules/Default/Registry.php';

        $this->view->title = Conjoon_Modules_Default_Registry::get(
            '/base/conjoon/name'
        );
    }

    /**
     * Default action for href-attributes that contained a link in the pattern
     * of "href='javascript:...'". Every link from cross domains that gets intercepted
     * should be edited to link to this action. The view will notify the user
     * of the inproper link.
     */
    public function javascriptAction()
    {


    }

    /**
     * Default action for redirecting to links not part of the conjoon application.
     *
     */
    public function redirectAction()
    {
        $link = $this->_request->getParam('url');
        $this->_redirect(urldecode($link));

        die();
    }
}