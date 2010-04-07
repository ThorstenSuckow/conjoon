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
        $conjoonContext->addActionContext('index',           self::CONTEXT_IPHONE)
                       ->addActionContext('post.bug.report', self::CONTEXT_JSON)
                       ->addActionContext('post.suggestion', self::CONTEXT_JSON)
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

    /**
     * Posts a bug report to the conjoon forums.
     *
     */
    public function postBugReportAction()
    {
        $emailAddress       = $this->_request->getParam('emailAddress');
        $problemDescription = $this->_request->getParam('problemDescription');
        $problemType        = $this->_request->getParam('problemType');
        $public             = $this->_request->getParam('public');
        $name               = $this->_request->getParam('name');

        /**
         * @see Zend_Http_Client
         */
        require_once 'Zend/Http/Client.php';

        $http = new Zend_Http_Client();

        $http->setUri('http://www.conjoon.org/forum/newthread.php?do=postthread&f=5');

        $http->setParameterPost(array(
            'do'                     => 'postthread',
            'f'                      => 5,
            'loggedinuser'           => 0,
            'conjoonGuid'            => 'f0c20c71-d95a-4d08-b01c-e41c5a6ae327',
            'securitytoken'          => 'guest',
            'username'               => 'Unregistered',
            'subject'                => "Component: \"".$problemType . "\" "
                                        . "from conjoon "
                                        . date("d.m.Y H:i:s", time()),
            'message'                => "Public: ".($public ? 'Yes' : 'No')."\n\n"
                                        . $problemDescription."\n\nFiled by ".$name
                                        ." <".$emailAddress.">"
        ));

        $http->setMethod(Zend_Http_Client::POST);

        $httpResponse = $http->request();
    }

    /**
     * Posts a feature suggestion to the conjoon forums.
     *
     */
    public function postSuggestionAction()
    {
        $emailAddress          = $this->_request->getParam('emailAddress');
        $suggestionDescription = $this->_request->getParam('suggestionDescription');
        $suggestionType        = $this->_request->getParam('suggestionType');
        $public                = $this->_request->getParam('public');
        $name                  = $this->_request->getParam('name');

        /**
         * @see Zend_Http_Client
         */
        require_once 'Zend/Http/Client.php';

        $http = new Zend_Http_Client();

        $http->setUri('http://www.conjoon.org/forum/newthread.php?do=postthread&f=6');

        $http->setParameterPost(array(
            'do'                     => 'postthread',
            'f'                      => 6,
            'loggedinuser'           => 0,
            'conjoonGuid'            => 'f0c20c71-d95a-4d08-b01c-e41c5a6ae327',
            'securitytoken'          => 'guest',
            'username'               => 'Unregistered',
            'subject'                => "Component: \"".$suggestionType . "\" "
                                        . "from conjoon "
                                        . date("d.m.Y H:i:s", time()),
            'message'                => "Public: ".($public ? 'Yes' : 'No')."\n\n"
                                        . $suggestionDescription."\n\nSuggested by "
                                        . $name . " <" . $emailAddress .">"
        ));

        $http->setMethod(Zend_Http_Client::POST);

        $httpResponse = $http->request();
    }

}