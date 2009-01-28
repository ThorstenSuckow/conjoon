<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
class Service_TwitterController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $contextSwitch = $this->_helper->contextSwitch();

        $contextSwitch->addActionContext('get.recent.tweets', self::CONTEXT_JSON)
                      ->addActionContext('get.friends',       self::CONTEXT_JSON)
                      ->addActionContext('get.accounts',      self::CONTEXT_JSON)
                      ->addActionContext('send.update',       self::CONTEXT_JSON)
                      ->addActionContext('get.users.recent.tweets', self::CONTEXT_JSON)
                      ->initContext();
    }


    /**
     * Sends account informations to the client.
     * Passwords will be masked. This action will also try to load
     * the user information for each account from the Twitter Service.
     * If this fails, the "twitter*" properties of the data which is to be
     * send to the client will be empty.
     *
     */
    public function getAccountsAction()
    {
        require_once 'Conjoon/Keys.php';
        $user = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $data = $decoratedModel->getAccountsForUserAsDto($user->getId());

        require_once 'Zend/Service/Twitter.php';

        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $dto =& $data[$i];

            try {
                /**
                 * @todo move to separate model
                 */
                $twitter = new Zend_Service_Twitter($dto->name, $dto->password);
                $response = $twitter->userShow($dto->name);

                $dto->twitterId              = (string)$response->id;
                $dto->twitterName            = (string)$response->name;
                $dto->twitterScreenName      = (string)$response->screen_name;
                $dto->twitterLocation        = (string)$response->location;
                $dto->twitterProfileImageUrl = (string)$response->profile_image_url;
                $dto->twitterUrl             = (string)$response->url;
                $dto->twitterProtected       = (bool)(string)$response->protected;
                $dto->twitterDescription     = (string)$response->description;
                $dto->twitterFollowersCount  = (int)(string)$response->followers_count;

                $twitter->accountEndSession();

            } catch (Exception $e) {
                // ignore
            }

            $dto->password = str_pad("", strlen($dto->password), '*');
        }

        $this->view->success  = true;
        $this->view->accounts = $data;
        $this->view->error    = null;
    }


    /**
     * Sends a list of recent tweets for the specified account to the client.
     * Expects the parameter "id" which holds the id of the configured account
     * stored in the database.
     *
     */
    public function getRecentTweetsAction()
    {
        $accountId = (int)$this->_request->getParam('id');

        if ($accountId <= 0) {
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = null;
            return;
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $accountDto = $decoratedModel->getAccountAsDto($accountId);

        if (!$accountDto) {
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = null;
            return;
        }

        require_once 'Zend/Service/Twitter.php';

        $twitter = new Zend_Service_Twitter($accountDto->name, $accountDto->password);

        $response = $twitter->status->friendsTimeline();

        $tweets = array();

        foreach ($response->status as $tweet) {
            $tweets[] = array(
                'id'              => (int)(string)$tweet->id,
                'text'            => (string)$tweet->text,
                'createdAt'       => (string)$tweet->created_at,
                'source'          => (string)$tweet->source,
                'truncated'       => (bool)(string)$tweet->truncated,

                'userId'          => (int)(string)$tweet->user->id,
                'name'            => (string)$tweet->user->name,
                'screenName'      => (string)$tweet->user->screen_name,
                'location'        => (string)$tweet->user->location,
                'profileImageUrl' => (string)$tweet->user->profile_image_url,
                'url'             => (string)$tweet->user->url,
                'description'     => (string)$tweet->user->description,
                'protected'       => (bool)(string)$tweet->user->protected,
                'followersCount'  => (int)(string)$tweet->user->followers_count
            );
        }

        $this->view->success = true;
        $this->view->tweets  = $tweets;
        $this->view->error   = null;
    }

    /**
     * Sends a list of friends for the specified account to the client.
     * Expects the parameter "id" which holds the id of the configured account
     * stored in the database.
     *
     */
    public function getFriendsAction()
    {
        $accountId = (int)$this->_request->getParam('id');

        if ($accountId <= 0) {
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = null;
            return;
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $accountDto = $decoratedModel->getAccountAsDto($accountId);

        if (!$accountDto) {
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = null;
            return;
        }

        require_once 'Zend/Service/Twitter.php';

        $twitter = new Zend_Service_Twitter($accountDto->name, $accountDto->password);

        // retrieve the 100 firends
        $friends = $twitter->user->friends();

        // Loop through results:
        $users = array();

        if ($friends->user) {
            foreach ($friends->user as $friend) {
                $users[] = array(
                    'id'              => (int)(string)$friend->id,
                    'name'            => (string)$friend->name,
                    'screenName'      => (string)$friend->screen_name,
                    'location'        => (string)$friend->location,
                    'profileImageUrl' => (string)$friend->profile_image_url,
                    'url'             => (string)$friend->url,
                    'description'     => (string)$friend->description,
                    'protected'       => (string)$friend->protected,
                    'followersCount'  => (int)(string)$friend->followers_count
                );
            }
        }

        $twitter->account->endSession();

        $this->view->success = true;
        $this->view->users   = $users;
        $this->view->error   = null;
    }

    /**
     * Sends a list of recent tweets for a Twitter user which id is specified
     * in the request parameter "userIdd". the account which triggered this request
     * is specified in the request parameter "id".
     *
     */
    public function getUsersRecentTweetsAction()
    {
        $userId    = (int)$this->_request->getParam('userId');
        $accountId = (int)$this->_request->getParam('id');

        if ($accountId <= 0) {
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = null;
            return;
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $accountDto = $decoratedModel->getAccountAsDto($accountId);

        if (!$accountDto) {
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = null;
            return;
        }

        require_once 'Zend/Service/Twitter.php';

        $twitter = new Zend_Service_Twitter($accountDto->name, $accountDto->password);

        $response = $twitter->status->userTimeline(array(
            'id' => $userId
        ));

        $tweets = array();

        foreach ($response->status as $tweet) {
            $tweets[] = array(
                'id'              => (int)(string)$tweet->id,
                'text'            => (string)$tweet->text,
                'createdAt'       => (string)$tweet->created_at,
                'source'          => (string)$tweet->source,
                'truncated'       => (bool)(string)$tweet->truncated,

                'userId'          => (int)(string)$tweet->user->id,
                'name'            => (string)$tweet->user->name,
                'screenName'      => (string)$tweet->user->screen_name,
                'location'        => (string)$tweet->user->location,
                'profileImageUrl' => (string)$tweet->user->profile_image_url,
                'url'             => (string)$tweet->user->url,
                'description'     => (string)$tweet->user->description,
                'protected'       => (bool)(string)$tweet->user->protected,
                'followersCount'  => (int)(string)$tweet->user->followers_count
            );
        }

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->tweets  = $tweets;
    }


    /**
     * Sends a message to Twitter for the configured account. The account
     * used to send the message is passed as an id in accountId
     *
     */
    public function sendUpdateAction()
    {
        $accountId = (int)$this->_request->getParam('accountId');
        $message   = (string)$this->_request->getParam('message');

        if ($accountId <= 0) {
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = null;
            return;
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $accountDto = $decoratedModel->getAccountAsDto($accountId);

        if (!$accountDto) {
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = null;
            return;
        }

        require_once 'Conjoon/Service/Twitter.php';

        $twitter = new Conjoon_Service_Twitter($accountDto->name, $accountDto->password);
        $response = $twitter->statusUpdate(
            $message
        );

        var_dump($response);

    }

}