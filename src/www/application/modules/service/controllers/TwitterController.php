<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Service_TwitterController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * @var array
     */
    protected $twitterProxyCache = array();

    /**
     * @var array
     */
    protected $accountDtoCache = array();

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('get.recent.tweets',       self::CONTEXT_JSON)
                       ->addActionContext('get.friends',             self::CONTEXT_JSON)
                       ->addActionContext('send.update',             self::CONTEXT_JSON)
                       ->addActionContext('delete.tweet',            self::CONTEXT_JSON)
                       ->addActionContext('favorite.tweet',          self::CONTEXT_JSON)
                       ->addActionContext('switch.friendship',       self::CONTEXT_JSON)
                       ->addActionContext('get.users.recent.tweets', self::CONTEXT_JSON)
                       ->initContext();
    }

    /**
     * Sends a list of recent tweets for the specified account to the client.
     * Expects the parameter "id" which holds the id of the configured account
     * stored in the database.
     *
     */
    public function getRecentTweetsAction()
    {
        /*@REMOVE@*/
        if (!$this->_helper->connectionCheck()) {
            $this->view->success = true;
            $this->view->tweets  = array();
            $this->view->error   = null;

            return;
        }
        /*@REMOVE@*/

        /**
         * @see Conjoon_Error_Factory
         */
        require_once 'Conjoon/Error/Factory.php';

        $accountId = (int)$this->_request->getParam('id');

        $twitter = $this->getTwitterProxy($accountId);

        if ($twitter instanceof Conjoon_Error) {
            $this->view->success = false;
            $this->view->tweets = array();
            $this->view->error = $twitter->getDto();
            return;
        }

        $tweets = $twitter->statusesHomeTimeline();

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

        $twitter = $this->getTwitterProxy($accountId);

        if ($twitter instanceof Conjoon_Error) {
            $this->view->success = false;
            $this->view->tweets = array();
            $this->view->error = $twitter->getDto();
            return;
        }

        $accountDto = $this->getAccountDto($accountId);

        if ($accountDto instanceof Conjoon_Error) {
            $this->view->success = false;
            $this->view->tweets = array();
            $this->view->error = $accountDto->getDto();
            return;
        }

        $users = $twitter->friendsList($accountDto->twitterId);

        if ($users instanceof Conjoon_Error) {
            $this->view->success = false;
            $this->view->users   = array();
            $this->view->error   = $users->getDto();
            return;
        }

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

        $userId    = (string)$this->_request->getParam('userId');
        $accountId = (int)$this->_request->getParam('id');
        $userName  = (string)$this->_request->getParam('userName');
        $statusId  = (string)$this->_request->getParam('statusId');

        if ($userName != "" && $userId == 0) {
            $userId = $userName;
        }

        if (!is_string($userId) && $userId == 0) {
            $errorTxt = "Could not receive tweets: No user-id or screen-name provided.";

            $errorDto = Conjoon_Error_Factory::createError(
                $errorTxt, Conjoon_Error::LEVEL_ERROR
            )->getDto();
            $this->view->success = false;
            $this->view->tweets  = array();
            $this->view->error   = $errorDto;
            return;
        }

        $twitter = $this->getTwitterProxy($accountId);

        if ($twitter instanceof Conjoon_Error) {
            $this->view->success = false;
            $this->view->tweets = array();
            $this->view->error = $twitter->getDto();
            return;
        }


        if ($statusId > 0) {
            $tweets = $twitter->statusesShow($statusId);
        } else {

            $ps = is_numeric($userId)
                  ? array('user_id'      => $userId)
                  : array('screen_name' => $userId);

            $tweets = $twitter->statusesUserTimeline($ps);
        }

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
        $inReplyToStatusId = (string)$this->_request->getParam('inReplyToStatusId');
        $message           = (string)$this->_request->getParam('message');

        $twitter = $this->getTwitterProxy($accountId);

        if ($twitter instanceof Conjoon_Error) {
            $this->view->success = false;
            $this->view->tweets = array();
            $this->view->error = $twitter->getDto();
            return;
        }

        // check inReplyToStatusId and set to null if necessary
        $inReplyToStatusId = $inReplyToStatusId > 0 ? $inReplyToStatusId : null;

        $result = $twitter->statusesUpdate($message, $inReplyToStatusId);

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
        $tweetId    = (string)$this->_request->getParam('tweetId');

        if ($tweetId == 0) {
            $errorDto = Conjoon_Error_Factory::createError(
                "Could not delete the tweet: No tweet specified",
                Conjoon_Error::LEVEL_ERROR
            )->getDto();

            $this->view->success      = false;
            $this->view->deletedTweet = null;
            $this->view->error        = $errorDto;
            return;
        }

        $twitter = $this->getTwitterProxy($accountId);

        if ($twitter instanceof Conjoon_Error) {
            $this->view->success = false;
            $this->view->deletedTweet = null;
            $this->view->error = $twitter->getDto();
            return;
        }

        $result  = $twitter->deleteTweet($tweetId);

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
        $tweetId    = (string)$this->_request->getParam('tweetId');
        /**
         * @todo Filter!!!
         */
        $favorite = !$this->_request->getParam('favorite')
                    ? false
                    : true;

        if ($tweetId == 0) {
            $this->view->success = false;
            $this->view->error = Conjoon_Error_Factory::createError(
                 "Could not process the request: No tweet specified.",
                Conjoon_Error::LEVEL_ERROR
            )->getDto();
            $this->view->favoritedTweet = null;
        }

        $twitter = $this->getTwitterProxy($accountId);

        if ($twitter instanceof Conjoon_Error) {
            $this->view->success = false;
            $this->view->favoritedTweet = null;
            $this->view->error = $twitter->getDto();
            return;
        }

        $result  = $twitter->favoriteTweet($tweetId, $favorite);

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

        $twitter = $this->getTwitterProxy($accountId);

        if ($twitter instanceof Conjoon_Error) {
            $this->view->success     = false;
            $this->view->isFollowing = !$createFriendship;
            $this->view->error       = $twitter->getDto();
            return;
        }

        if ($createFriendship) {
            $result = $twitter->friendshipsCreate($screenName);
        } else {
            $result = $twitter->friendshipsDestroy($screenName);
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

    /**
     * Returns the account dto for the specified account id, or an instance of
     * Conjoon_Error if an error occurs
     *
     * @param $accountId
     * @return account dto or an instance of Conjoon_Error
     *
     */
    protected function getAccountDto($accountId) {

        if (isset($this->accountDtoCache[$accountId])) {
            return $this->accountDtoCache[$accountId];
        }

        /**
         * @see Conjoon_Error_Factory
         */
        require_once 'Conjoon/Error/Factory.php';

        if ($accountId <= 0) {
            $error = Conjoon_Error_Factory::createError(
                "Could not process the request: No account-id provided.",
                Conjoon_Error::LEVEL_ERROR
            )->getDto();

            return $error;
        }

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        $decoratedModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Service_Twitter_Account_Model_Account'
        );

        $accountDto = $decoratedModel->getAccountAsDto($accountId);

        if (!$accountDto) {
            $error = Conjoon_Error_Factory::createError(
                "Could not switch friendship: No account matches " .
                    "the id \"".$accountId."\".", Conjoon_Error::LEVEL_CRITICAL
            );

            return $error;
        }

        return $this->accountDtoCache[$accountId] = $accountDto;

        return $accountDto;
    }


    /**
     * Returns an instance of Conjoon_Service_Twitter_Proxy.
     *
     * @param mixed $accountId
     *
     * @return Conjoon_Service_Twitter_Proxy or an instance of Conjoon_Error
     */
    protected function getTwitterProxy($accountId) {

        if (isset($this->twitterProxyCache[$accountId])) {
            return $this->twitterProxyCache[$accountId];
        }

        $accountDto = $this->getAccountDto($accountId);

        if ($accountDto instanceof Conjoon_Error) {
            return $accountDto;
        }

        /**
         * @see Conjoon_Service_Twitter_Proxy
         */
        require_once 'Conjoon/Service/Twitter/Proxy.php';

        /**
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $config = Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT);

        $consumerKey    = $config->application->twitter->oauth->consumerKey;
        $consumerSecret = $config->application->twitter->oauth->consumerSecret;

        /**
         * @see Conjoon_Modules_Default_Registry_Facade
         */
        require_once 'Conjoon/Modules/Default/Registry/Facade.php';

        $protocolContext = Conjoon_Modules_Default_Registry_Facade::getInstance()
            ->getValueForKeyAndUserId(
                '/server/environment/protocol',
                $this->_helper->registryAccess()->getUserId()
            );

        $twitter = new Conjoon_Service_Twitter_Proxy(array(
            'oauth_token'        => $accountDto->oauthToken,
            'oauth_token_secret' => $accountDto->oauthTokenSecret,
            'user_id'            => $accountDto->twitterId,
            'screen_name'        => $accountDto->name,
            'consumer_key'       => $consumerKey,
            'consumer_secret'    => $consumerSecret,
            'protocol_context'   => $protocolContext
        ));

        $this->twitterProxyCache[$accountId] = $twitter;

        return $twitter;
    }

}
