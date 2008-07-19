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

    public function phpinfoAction()
    {
        echo phpinfo();
        die();
    }

    public function sandboxAction()
    {
       // throw new Exception("YO");
       // header('Content-Type: text/html; charset=utf-8');
        /*
        require_once 'Intrabuild/Modules/Groupware/Email/Item/Model/Inbox.php';

        $itemModel = new Intrabuild_Modules_Groupware_Email_Item_Model_Inbox();

        $res = $itemModel->getLatestItemCount(1, 0);

        echo "<pre>";
        var_dump($res);
        */


        /*require_once 'Intrabuild/Keys.php';
        require_once 'Intrabuild/Modules/Groupware/Email/Letterman.php';

        $auth   = Zend_Registry::get(Intrabuild_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();
        Intrabuild_Modules_Groupware_Email_Letterman::fetchEmails($userId);

        die();   */
    }

    public function indexAction()
    {

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