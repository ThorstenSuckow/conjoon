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
        $conjoonContext->addActionContext('index',         self::CONTEXT_IPHONE)
                       ->addActionContext('post.feedback', self::CONTEXT_JSON)
                       ->initContext();
    }

    public function indexAction()
    {
         $userId = $this->_helper->registryAccess()->getUserId();

        /**
         * @see Conjoon_Modules_Default_Registry_Facade
         */
        require_once 'Conjoon/Modules/Default/Registry/Facade.php';

        $this->view->title = Conjoon_Modules_Default_Registry_Facade::getInstance()
                             ->getValueForKeyAndUserId('/base/conjoon/name', $userId);

        $this->view->softwareLabel = Conjoon_Modules_Default_Registry_Facade::getInstance()
                                     ->getValueForKeyAndUserId('/base/conjoon/name', $userId);
        $this->view->editionLabel = Conjoon_Modules_Default_Registry_Facade::getInstance()
                                    ->getValueForKeyAndUserId('/base/conjoon/edition', $userId);

        /**
         * @see Conjoon_Version
         */
        require_once 'Conjoon/Version.php';

        $this->view->versionLabel = Conjoon_Version::VERSION;

        // check if there are any GET params available. If that is the case,
        // we won't deliver the app cache manifest with the page
        if (!empty($_GET)) {
            $this->view->enableManifest = false;
        } else {
            $this->view->enableManifest = true;
        }
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

    /**
     * Posts a bug report to the conjoon forums.
     *
     */
    public function postFeedbackAction()
    {
        $emailAddress        = $this->_request->getParam('emailAddress');
        $feedbackType        = $this->_request->getParam('feedbackType');
        $feedbackDescription = $this->_request->getParam('feedbackDescription');
        $component           = $this->_request->getParam('component');
        $public              = $this->_request->getParam('public');
        $name                = $this->_request->getParam('name');

        /**
         * @see Zend_Http_Client
         */
        require_once 'Zend/Http/Client.php';

        $http = new Zend_Http_Client();

        if ($feedbackType == 'bug') {
            $http->setUri('http://conjoon.org/forum/newthread.php?do=postthread&f=5');
        } else {
            $http->setUri('http://conjoon.org/forum/newthread.php?do=postthread&f=6');
        }


        $http->setParameterPost(array(
            'do'                     => 'postthread',
            'f'                      => $feedbackType == 'bug' ? 5 : 6,
            'loggedinuser'           => 0,
            'conjoonGuid'            => 'f0c20c71-d95a-4d08-b01c-e41c5a6ae327',
            'securitytoken'          => 'guest',
            'username'               => 'Unregistered',
            'subject'                => ($feedbackType == 'imhappy'
                                         ? 'imhappy! ' : '')
                                        . "Component: \"".$component . "\" "
                                        . "from conjoon "
                                        . date("d.m.Y H:i:s", time()),
            'message'                => "Public: ".($public ? 'Yes' : 'No')."\n\n"
                                        . $feedbackDescription."\n\nFiled by ".$name
                                        ." <".$emailAddress.">"
        ));

        $http->setMethod(Zend_Http_Client::POST);

        $http->request();
    }




}