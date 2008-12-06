<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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

    public function indexAction()
    {
        /**
         * @see Intrabuild_Modules_Default_Registry
         */
        require_once 'Intrabuild/Modules/Default/Registry.php';

        $this->view->title = Intrabuild_Modules_Default_Registry::get(
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
     * Default action for redirecting to links not part of the intrabuild application.
     *
     */
    public function redirectAction()
    {
        $link = $this->_request->getParam('url');
        $this->_redirect(urldecode($link));

        die();
    }
}