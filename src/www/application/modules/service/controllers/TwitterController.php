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
        $contextSwitch = $this->_helper->conjoonContext();

        $contextSwitch->addActionContext('get.recent.tweets', self::CONTEXT_JSON)
                      ->addActionContext('get.friends',       self::CONTEXT_JSON)
                      ->addActionContext('get.accounts',      self::CONTEXT_JSON)
                      ->addActionContext('send.update',       self::CONTEXT_JSON)
                      ->addActionContext('delete.tweet',      self::CONTEXT_JSON)
                      ->addActionContext('favorite.tweet',    self::CONTEXT_JSON)
                      ->addActionContext('switch.friendship', self::CONTEXT_JSON)
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
        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $user   = Zend_Registry::get(
            Conjoon_Keys::REGISTRY_AUTH_OBJECT
        )->getIdentity();

        $userId = $user->getId();

        /**
         * @see Conjoon_Builder_Factory
         */
        require_once 'Conjoon/Builder/Factory.php';

        $data = Conjoon_Builder_Factory::getBuilder(
            Conjoon_Keys::CACHE_TWITTER_ACCOUNTS,
            Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
        )->get(array('userId' => $userId));

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
        /**
         * @see Conjoon_Error_Factory
         */
        require_once 'Conjoon/Error/Factory.php';

        $accountId = (int)$this->_request->getParam('id');

        if ($accountId <= 0) {
            $errorDto = Conjoon_Error_Factory::createError(
                "Could not send the status update: No account-id provided.", Conjoon_Error::LEVEL_ERROR
            )->getDto();
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = $errorDto;
            return;
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $accountDto = $decoratedModel->getAccountAsDto($accountId);

        if (!$accountDto) {
            $errorDto = Conjoon_Error_Factory::createError(
                "Could not retrieve tweets: No account matches the id \"".$accountId."\".", Conjoon_Error::LEVEL_CRITICAL
            )->getDto();
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = $errorDto;
            return;
        }

        /**
         * @see Conjoon_Service_Twitter_Proxy
         */
        require_once 'Conjoon/Service/Twitter/Proxy.php';

        $twitter = new Conjoon_Service_Twitter_Proxy(
            $accountDto->name, $accountDto->password
        );

        $tweets = $twitter->statusFriendsTimeline();
        $twitter->accountEndSession();

        if ($tweets instanceof Conjoon_Error) {
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = $tweets->getDto();
            return;
        }

        $dtoTweets = array();

        for ($i = 0, $len = count($tweets); $i < $len; $i++) {
            $dtoTweets[] = $tweets[$i]->getDto();
        }

        $this->view->success = true;
        $this->view->tweets  = $dtoTweets;
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
                    'id'              => (float)(string)$friend->id,
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
     * in the request parameter "userId". the account which triggered this request
     * is specified in the request parameter "id".
     * If the parameter statusId is supplied, only this single entry will be returned.
     *
     */
    public function getUsersRecentTweetsAction()
    {
        /**
         * @see Conjoon_Error_Factory
         */
        require_once 'Conjoon/Error/Factory.php';

        $userId    = (float)$this->_request->getParam('userId');
        $accountId = (int)$this->_request->getParam('id');
        $userName  = (string)$this->_request->getParam('userName');
        $statusId  = (float)$this->_request->getParam('statusId');

        if ($userName != "" && $userId <= 0) {
            $userId = $userName;
        }

        if ($accountId <= 0 || (!is_string($userId) && $userId <= 0)) {
            $errorTxt = $accountId <= 0
                        ? "Could not receive tweets: No account-id provided."
                        : "Could not receive tweets: No user-id or screen-name provided.";

            $errorDto = Conjoon_Error_Factory::createError(
                $errorTxt, Conjoon_Error::LEVEL_ERROR
            )->getDto();
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = $errorDto;
            return;
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $accountDto = $decoratedModel->getAccountAsDto($accountId);

        if (!$accountDto) {
            $errorDto = Conjoon_Error_Factory::createError(
                "Could not retrieve tweets: No account matches the id \"".$accountId."\".", Conjoon_Error::LEVEL_CRITICAL
            )->getDto();
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = $errorDto;
            return;
        }

        /**
         * @see Conjoon_Service_Twitter_Proxy
         */
        require_once 'Conjoon/Service/Twitter/Proxy.php';

        $twitter = new Conjoon_Service_Twitter_Proxy(
            $accountDto->name, $accountDto->password
        );

        if ($statusId > 0) {
            $tweets = $twitter->statusShow($statusId);
        } else {
            $tweets = $twitter->statusUserTimeline(array(
                'id' => $userId
            ));
        }

        $twitter->accountEndSession();

        if ($tweets instanceof Conjoon_Error) {
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = $tweets->getDto();
            return;
        }

        $dtoTweets = array();

        if ($statusId > 0) {
            $dtoTweets[] = $tweets->getDto();
        } else {
            for ($i = 0, $len = count($tweets); $i < $len; $i++) {
                $dtoTweets[] = $tweets[$i]->getDto();
            }
        }

        $this->view->success = true;
        $this->view->tweets  = $dtoTweets;
        $this->view->error   = null;
    }


    /**
     * Sends a message to Twitter for the configured account. The account
     * used to send the message is passed as an id in accountId
     *
     */
    public function sendUpdateAction()
    {
        /**
         * @see Conjoon_Error_Factory
         */
        require_once 'Conjoon/Error/Factory.php';

        $accountId         = (int)$this->_request->getParam('accountId');
        $inReplyToStatusId = (float)$this->_request->getParam('inReplyToStatusId');
        $message            = (string)$this->_request->getParam('message');

        if ($accountId <= 0) {
            $errorDto = Conjoon_Error_Factory::createError(
                "Could not send the status update: No account-id provided.", Conjoon_Error::LEVEL_ERROR
            )->getDto();

            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = $errorDto;
            return;
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $accountDto = $decoratedModel->getAccountAsDto($accountId);

        if (!$accountDto) {
            $errorDto = Conjoon_Error_Factory::createError(
                "Could not send the status update: No account matches the id \"".$accountId."\".", Conjoon_Error::LEVEL_CRITICAL
            )->getDto();

            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = $errorDto;
            return;
        }

        require_once 'Conjoon/Service/Twitter/Proxy.php';

        $twitter = new Conjoon_Service_Twitter_Proxy($accountDto->name, $accountDto->password);

        // check inReplyToStatusId and set to null if necessary
        $inReplyToStatusId = $inReplyToStatusId > 0 ? $inReplyToStatusId : null;

        $result  = $twitter->statusUpdate($message, $inReplyToStatusId);
        $twitter->accountEndSession();

        if ($result instanceof Conjoon_Error) {
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = $result->getDto();
            return;
        }

        $this->view->success = true;
        $this->view->tweet   = $result->getDto();
        $this->view->error   = null;
    }

    /**
     * requests to delete a tweet with a specific id.
     * The acount used to request the delete is passed as an id in accountId,
     * the id of the tweet to delete is passed in the parameter tweetId.
     *
     */
    public function deleteTweetAction()
    {
        /**
         * @see Conjoon_Error_Factory
         */
        require_once 'Conjoon/Error/Factory.php';

        $accountId  = (int)$this->_request->getParam('accountId');
        $tweetId    = (float)$this->_request->getParam('tweetId');

        if ($accountId <= 0 || $tweetId <= 0) {
            $errorDto = Conjoon_Error_Factory::createError(
                (($accountId <= 0)
                ? "Could not delete the tweet: No account-id provided."
                : "Could not delete the tweet: No tweet specified."),
                Conjoon_Error::LEVEL_ERROR
            )->getDto();

            $this->view->success      = false;
            $this->view->deletedTweet = null;
            $this->view->error        = $errorDto;
            return;
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $accountDto = $decoratedModel->getAccountAsDto($accountId);

        if (!$accountDto) {
            $errorDto = Conjoon_Error_Factory::createError(
                "Could not delete the tweet: No account matches the id \"".$accountId."\".", Conjoon_Error::LEVEL_CRITICAL
            )->getDto();

            $this->view->success      = false;
            $this->view->deletedTweet = null;
            $this->view->error        = $errorDto;
            return;
        }

        require_once 'Conjoon/Service/Twitter/Proxy.php';

        $twitter = new Conjoon_Service_Twitter_Proxy($accountDto->name, $accountDto->password);

        $result  = $twitter->deleteTweet($tweetId);
        $twitter->accountEndSession();

        if ($result instanceof Conjoon_Error) {
            $this->view->success      = false;
            $this->view->deletedTweet = null;
            $this->view->error        = $result->getDto();
            return;
        }

        $this->view->success      = true;
        $this->view->deletedTweet = $result->getDto();
        $this->view->error        = null;
    }

    /**
     * requests to favorite a tweet with a specific id.
     * The acount used to request the "favorite" is passed as an id in accountId,
     * the id of the tweet to favorite is passed in the parameter tweetId.
     * The boolean parameter "favorite" tells whether to "favorite" or
     * "un-favorite" the specified tweet.
     *
     */
    public function favoriteTweetAction()
    {
        /**
         * @see Conjoon_Error_Factory
         */
        require_once 'Conjoon/Error/Factory.php';

        $accountId  = (int)$this->_request->getParam('accountId');
        $tweetId    = (float)$this->_request->getParam('tweetId');
        /**
         * @todo Filter!!!
         */
        $favorite = !$this->_request->getParam('favorite')
                    ? false
                    : true;

        if ($accountId <= 0 || $tweetId <= 0) {
            $errorDto = Conjoon_Error_Factory::createError(
                (($accountId <= 0)
                ? "Could not process the request: No account-id provided."
                : "Could not process the request: No tweet specified."),
                Conjoon_Error::LEVEL_ERROR
            )->getDto();

            $this->view->success        = false;
            $this->view->favoritedTweet = null;
            $this->view->error          = $errorDto;
            return;
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $accountDto = $decoratedModel->getAccountAsDto($accountId);

        if (!$accountDto) {
            $errorDto = Conjoon_Error_Factory::createError(
                "Could not favorite the tweet: No account matches the id \"".$accountId."\".", Conjoon_Error::LEVEL_CRITICAL
            )->getDto();

            $this->view->success        = false;
            $this->view->favoritedTweet = null;
            $this->view->error          = $errorDto;
            return;
        }

        require_once 'Conjoon/Service/Twitter/Proxy.php';

        $twitter = new Conjoon_Service_Twitter_Proxy($accountDto->name, $accountDto->password);

        $result  = $twitter->favoriteTweet($tweetId, $favorite);
        $twitter->accountEndSession();

        if ($result instanceof Conjoon_Error) {
            $this->view->success        = false;
            $this->view->favoritedTweet = null;
            $this->view->error          = $result->getDto();
            return;
        }

        $this->view->success        = true;
        $this->view->favoritedTweet = $result->getDto();
        $this->view->error          = null;
    }


    /**
     * Switches a friendship to a user based on the parameter createFriendship and
     * the screen name of the user.
     *
     */
    public function switchFriendshipAction()
    {
        /**
         * @see Conjoon_Error_Factory
         */
        require_once 'Conjoon/Error/Factory.php';

        $accountId  = (int)$this->_request->getParam('accountId');

        $createFriendship = $this->_request->getParam('createFriendship') == 'false'
                            ? false
                            : true;
        $screenName       = $this->_request->getParam('screenName');


        if ($accountId <= 0) {
            $errorDto = Conjoon_Error_Factory::createError(
                "Could not process the request: No account-id provided.",
                Conjoon_Error::LEVEL_ERROR
            )->getDto();

            $this->view->success     = false;
            $this->view->isFollowing = !$createFriendship;
            $this->view->error       = $errorDto;
            return;
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $accountDto = $decoratedModel->getAccountAsDto($accountId);

        if (!$accountDto) {
            $errorDto = Conjoon_Error_Factory::createError(
                "Could not switch friendship: No account matches the id \"".$accountId."\".", Conjoon_Error::LEVEL_CRITICAL
            )->getDto();

            $this->view->success     = false;
            $this->view->isFollowing = !$createFriendship;
            $this->view->error       = $errorDto;
            return;
        }

        require_once 'Conjoon/Service/Twitter/Proxy.php';

        $twitter = new Conjoon_Service_Twitter_Proxy(
            $accountDto->name,
            $accountDto->password
        );

        if ($createFriendship) {
            $result = $twitter->friendshipCreate($screenName);
        } else {
            $result = $twitter->friendshipDestroy($screenName);
        }

        if ($result instanceof Conjoon_Error) {
            $this->view->success     = false;
            $this->view->isFollowing = !$createFriendship;
            $this->view->error       = $result->getDto();
            return;
        }

        $this->view->success     = true;
        $this->view->isFollowing = $createFriendship;
        $this->view->error       = null;
    }


}