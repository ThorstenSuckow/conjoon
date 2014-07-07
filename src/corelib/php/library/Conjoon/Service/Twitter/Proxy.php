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
 * @see Conjoon_Service_Twitter
 */
require_once 'Conjoon/Service/Twitter.php';

/**
 * @see Zend_Oauth_Token_Access
 */
require_once 'Zend/Oauth/Token/Access.php';

/**
 * This class proxies requests to the Twitter service and takes care
 * of returning appropriate and easy to use objects depending on the
 * requested actions.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Service_Twitter_Proxy  {


    /**
     * @var Conjoon_Service_Twitter
     */
    private $_twitter;

    /**
     * @var string $protocolContext The protocol which is used with this twitter
     * proxy. Can either be http or https. Defaults to 'http'.
     */
    private $protocolContext = 'http';

    /**
     * Creates a new instance of Conjoon_Service_Twitter_Proxy
     *
     * @param array|Zend_Config a configuration object for this class, with at
     * least the following key/values:
     * - oauth_token - as provided by Twitter's oauth
     * - oauth_token_secret - as provided by Twitter's oauth
     * - user_id - id of the user as provided by the twitter service
     * - screen_name - screen name of the user as provided by the twitter
     *                 service
     * - protocol_context - The protocol used for gatering Twitter API data (images
     * and such). Can be http or https
     *
     * @throws InvalidArgumentException if $options was not of type
     * array or Zend_Config or if any expected key was missing in $options
     */
    public function __construct($options)
    {
        /**
         * @see Zend_Config
         */
        require_once 'Zend/Config.php';

        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            throw new InvalidArgumentException(
                "\"options\" was neither of type array nor of type Zend_Config"
            );
        }

        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';


        $whitelist = array(
            'oauth_token', 'oauth_token_secret', 'user_id', 'screen_name',
            'consumer_key', 'consumer_secret', 'protocol_context'
        );

        $accessTokenOptions = Conjoon_Util_Array::extractByKeys($options, $whitelist);

        if ($accessTokenOptions === false) {
            $exists = Conjoon_Util_Array::arrayKeysExist($options, $whitelist);
            if ($exists !== true) {
               throw new InvalidArgumentException(
                    "key \"$exists\" was not specified in options"
                );
            }
            throw new InvalidArgumentException(
                "could not extract whitelisted keys from options array"
            );
        }

        $protocolContext = isset($accessTokenOptions['protocol_context'])
                           ? $accessTokenOptions['protocol_context']
                           : $this->protocolContext;

        if (isset($accessTokenOptions['protocol_context'])) {
            unset($accessTokenOptions['protocol_context']);
        }

        if ($protocolContext !== 'http' && $protocolContext !== 'https') {
            throw new RuntimeException(
                "\"protocol_context\" was neither 'http' nor 'https'"
            );
        }

        $this->protocolContext = $protocolContext;

        /**
         * @see Zend_Oauth_Token_Access
         */
        require_once 'Zend/Oauth/Token/Access.php';

        $accessToken = new Zend_Oauth_Token_Access();
        $accessToken->setParams($accessTokenOptions);

        $this->_twitter = new Conjoon_Service_Twitter(array(
            'username'    => $accessTokenOptions['screen_name'],
            'accessToken' => $accessToken,
            'oauthOptions' => array(
                'consumerKey'    => $accessTokenOptions['consumer_key'],
                'consumerSecret' => $accessTokenOptions['consumer_secret']
            )
        ));
    }

    /**
     * Returns a Conjoon_Error based on the specified exception.
     *
     * @param Exception $e
     *
     * @return Conjoon_Error
     */
    protected function fromTwitterServiceException(Exception $e) {
        /**
         * @see Conjoon_Error_Factory
         */
        require_once 'Conjoon/Error/Factory.php';

        return Conjoon_Error_Factory::createError(
            $e->getMessage(), Conjoon_Error::LEVEL_ERROR
        );
    }

    /**
     * Returns an error object based on the error information found in the
     * response obejct.
     *
     * @param Zend_Service_Twitter_Response $respobnse
     *
     * @return Conjoon_Error
     *
     * @throws RuntimeException if neither errors or error is found
     * in the response object
     */
    protected function fromTwitterServiceError(Zend_Service_Twitter_Response $response) {
        /**
         * @see Conjoon_Error_Factory
         */
        require_once 'Conjoon/Error/Factory.php';


        if ($response->errors) {
            $errorStr = array();

            foreach ($response->errors as $value) {
                $errorStr[] = $value->message;
            }

            $errorStr = implode("\n", $errorStr);

            return Conjoon_Error_Factory::createError(
                $errorStr .
                    " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
        }

        if ($response->error) {
            return Conjoon_Error_Factory::createError(
                $response->error .
                    " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
        }

        throw new RuntimeException("errors or error was not set in the resposne object");
    }

    /**
     * Returns the list of user the specified user follows.
     *
     * @param mixed $id the twitter id of the user
     *
     * @return array an array with the users this user is following, or
     * Conjoon_Error if any error occurres
     */
    public function friendsList($id) {

        $cursor = -1;

        $users = array();


        while ($cursor != 0) {

            $response = $this->_twitter->friends->list(array(
                'user_id'   => $id,
                'cursor' => $cursor,
                'count' => 200
            ));

            if ($response->errors || $response->error) {
                return $this->fromTwitterServiceError($response);
            }

            $jsonBody = $response->toValue();

            if ($jsonBody->users) {

                // looks like we won't get an array if the twitter user
                // has only one friend. instead we'll get directly a
                // SimpleXMLElement
                if (!is_array($jsonBody->users)) {
                    $jsonBody->users = array($jsonBody->users);
                }

                foreach ($jsonBody->users as $friend) {

                    $users[] = array(
                        'id'              => (string)$friend->id_str,
                        'name'            => (string)$friend->name,
                        'screenName'      => (string)$friend->screen_name,
                        'location'        => (string)$friend->location,
                        'profileImageUrl' => $this->protocolContext === 'https'
                                             ? (string)$friend->profile_image_url_https
                                             : (string)$friend->profile_image_url,
                        'url'             => (string)$friend->url,
                        'description'     => (string)$friend->description,
                        'protected'       => (string)$friend->protected,
                        'followersCount'  => (int)(string)$friend->followers_count
                    );
                }
            } else {
                break;
            }

            $cursor = (string)$jsonBody->next_cursor_str;
        }

        return $users;
    }

    /**
     * Verifies account credentials.
     *
     * @return true or Conjoon_Modules_Service_Twitter_Account_Dto
     */
    public function accountVerifyCredentials()
    {
        try {
            $response = $this->_twitter->accountVerifyCredentials();
        } catch (Zend_Service_Twitter_Exception $e) {
            return $this->fromTwitterServiceException($e);
        }

        if ($response->errors || $response->error) {
            return $this->fromTwitterServiceError($response);
        }

        /**
         * @see Conjoon_Modules_Service_Twitter_Account_Dto
         */
        require_once 'Conjoon/Modules/Service/Twitter/Account/Dto.php';

        $dto = new Conjoon_Modules_Service_Twitter_Account_Dto;

        $dto->twitterId              = (string)$response->id_str;
        $dto->twitterName            = (string)$response->name;
        $dto->twitterScreenName      = (string)$response->screen_name;
        $dto->twitterLocation        = (string)$response->location;
        $dto->twitterProfileImageUrl = $this->protocolContext === 'https'
                                       ? (string)$response->profile_image_url_https
                                       : (string)$response->profile_image_url;
        $dto->twitterUrl             = (string)$response->url;
        $dto->twitterProtected       = (bool)(string)$response->protected;
        $dto->twitterDescription     = (string)$response->description;
        $dto->twitterFollowersCount  = (int)(string)$response->followers_count;

        return $dto;
    }

    /**
     * Create friendship
     *
     * @param  int|string $id User ID or name of new friend
     * @return true or Conjoon_Error
     */
    public function friendshipsCreate($id)
    {
        try {
            $response = $this->_twitter->friendshipsCreate($id);
        } catch (Zend_Service_Twitter_Exception $e) {
            return $this->fromTwitterServiceException($e);
        }

        if ($response->errors || $response->error) {
            return $this->fromTwitterServiceError($response);
        }

        return true;
    }

    /**
     * Destroy friendship
     *
     * @param  int|string $id User ID or name of friend to remove
     *
     * @return boolean true if friendship was destroyed, otherwise false. Returns
     * Conjoon_Error if anything fails
     */
    public function friendshipsDestroy($id)
    {
        try {
            $response = $this->_twitter->friendshipsDestroy($id);
        } catch (Zend_Service_Twitter_Exception $e) {
            return $this->fromTwitterServiceException($e);
        }

        if ($response->errors || $response->error) {
            return $this->fromTwitterServiceError($response);
        }

        return true;
    }

    /**
     * Favorites or unfavorites a tweet based on the $favorite
     * parameter.
     *
     * @todo "favorited" in xml response returns always false as of 12-July 2009
     * check back later for proper return value
     *
     * @param int $id Id of the tweet to (un)favorite
     * @param boolean $favorite true to favorite the tweet, false tounfavorite it
     *
     * @return Conjoon_Error if any error occured, otherwise
     * Conjoon_Modules_Service_Twitter_Tweet with the data of the (un)favorited tweet
     */
    public function favoriteTweet($id, $favorite = false)
    {
        try {
            if ($favorite) {
                $response = $this->_twitter->favoritesCreate($id);
            } else {
                $response = $this->_twitter->favoritesDestroy($id);
            }
        } catch (Zend_Service_Twitter_Exception $e) {
            return $this->fromTwitterServiceException($e);
        }

        if ($response->errors || $response->error) {
            return $this->fromTwitterServiceError($response);
        }

        $jsonBody = $response->toValue();

        $data = array(
            'id'                  => (string)$jsonBody->id_str,
            'text'                => (string)$jsonBody->text,
            'createdAt'           => (string)$jsonBody->created_at,
            'source'              => (string)$jsonBody->source,
            'truncated'           => (string)$jsonBody->truncated,
            'userId'              => (string)$jsonBody->user->id_str,
            'name'                => (string)$jsonBody->user->name,
            'screenName'          => (string)$jsonBody->user->screen_name,
            'location'            => (string)$jsonBody->user->location,
            'profileImageUrl'     => $this->protocolContext === 'https'
                                     ? (string)$jsonBody->user->profile_image_url_https
                                     : (string)$jsonBody->user->profile_image_url,
            'url'                 => (string)$jsonBody->user->url,
            'description'         => (string)$jsonBody->user->description,
            'protected'           => (string)$jsonBody->user->protected,
            'isFollowing'         => (string)$jsonBody->user->following,
            'followersCount'      => (string)$jsonBody->user->followers_count,
            'inReplyToStatusId'   => (string)$jsonBody->in_reply_to_status_id_str,
            'inReplyToUserId'     => (string)$jsonBody->in_reply_to_user_id_str,
            'inReplyToScreenName' => (string)$jsonBody->in_reply_to_screen_name,
            'favorited'           => $favorite
        );

        /**
         * @see Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet
         */
        require_once 'Conjoon/Modules/Service/Twitter/Tweet/Filter/Tweet.php';

        $filter = new Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet(
            $data, Conjoon_Filter_Input::CONTEXT_RESPONSE
        );

        $data = $filter->getProcessedData();
        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        $entity = Conjoon_BeanContext_Inspector::create(
            'Conjoon_Modules_Service_Twitter_Tweet',
             $data
        );


        return $entity;
    }

    /**
     * Destroy a status message.
     *
     * @param int $id ID of status to destroy
     *
     * @return Conjoon_Error if any error occured, otherwise
     * Conjoon_Modules_Service_Twitter_Tweet with the data of the deleted tweet
     */
    public function deleteTweet($id)
    {
        try {
            $response = $this->_twitter->statusesDestroy($id);
        } catch (Zend_Service_Twitter_Exception $e) {
            return $this->fromTwitterServiceException($e);
        }

        if ($response->errors || $response->error) {
            return $this->fromTwitterServiceError($response);
        }

        $jsonBody = $response->toValue();

        $data = array(
            'id'                  => (string)$jsonBody->id_str,
            'text'                => (string)$jsonBody->text,
            'createdAt'           => (string)$jsonBody->created_at,
            'source'              => (string)$jsonBody->source,
            'truncated'           => (string)$jsonBody->truncated,
            'userId'              => (string)$jsonBody->user->id_str,
            'name'                => (string)$jsonBody->user->name,
            'screenName'          => (string)$jsonBody->user->screen_name,
            'location'            => (string)$jsonBody->user->location,
            'profileImageUrl'     => $this->protocolContext === 'https'
                                     ? (string)$jsonBody->user->profile_image_url_https
                                     : (string)$jsonBody->user->profile_image_url,
            'url'                 => (string)$jsonBody->user->url,
            'description'         => (string)$jsonBody->user->description,
            'protected'           => (string)$jsonBody->user->protected,
            'isFollowing'         => (string)$jsonBody->user->following,
            'followersCount'      => (string)$jsonBody->user->followers_count,
            'inReplyToStatusId'   => (string)$jsonBody->in_reply_to_status_id_str,
            'inReplyToUserId'     => (string)$jsonBody->in_reply_to_user_id_str,
            'inReplyToScreenName' => (string)$jsonBody->in_reply_to_screen_name,
            'favorited'           => (string)$jsonBody->favorited
        );

        /**
         * @see Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet
         */
        require_once 'Conjoon/Modules/Service/Twitter/Tweet/Filter/Tweet.php';

        $filter = new Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet(
            $data, Conjoon_Filter_Input::CONTEXT_RESPONSE
        );

        $data = $filter->getProcessedData();
        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        $entity = Conjoon_BeanContext_Inspector::create(
            'Conjoon_Modules_Service_Twitter_Tweet',
             $data
        );


        return $entity;
    }

    /**
     * Show a single status
     *
     * @param  int $id Id of status to show
     * @return Conjoon_Error if any error occures, otherwise an instance of
     * Conjoon_Modules_Service_Twitter_Tweet
     */
    public function statusesShow($id)
    {
        try {
            $response = $this->_twitter->statusesShow($id);
        } catch (Zend_Service_Twitter_Exception $e) {
            return $this->fromTwitterServiceException($e);
        }

        if ($response->errors || $response->error) {
            return $this->fromTwitterServiceError($response);
        }

        /**
         * @see Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet
         */
        require_once 'Conjoon/Modules/Service/Twitter/Tweet/Filter/Tweet.php';

        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        $filter = new Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet(
            array(), Conjoon_Filter_Input::CONTEXT_RESPONSE
        );

        $jsonBody = $response->toValue();

        $tweetUserId = $jsonBody->user->id_str;

        $isFollowing = $this->amIFollowingThatUser(array('target_id' => $tweetUserId));

        if (!is_bool($isFollowing)) {
            return $isFollowing;
        }

        $data = array(
            'id'                  => $jsonBody->id_str,
            'text'                => $jsonBody->text,
            'createdAt'           => $jsonBody->created_at,
            'source'              => $jsonBody->source,
            'truncated'           => $jsonBody->truncated,
            'userId'              => $tweetUserId,
            'name'                => $jsonBody->user->name,
            'screenName'          => $jsonBody->user->screen_name,
            'location'            => $jsonBody->user->location,
            'profileImageUrl'     => $this->protocolContext === 'https'
                                     ? $jsonBody->user->profile_image_url_https
                                     : $jsonBody->user->profile_image_url,
            'url'                 => $jsonBody->user->url,
            'description'         => $jsonBody->user->description,
            'protected'           => $jsonBody->user->protected,
            'isFollowing'         => $isFollowing,
            'followersCount'      => $jsonBody->user->followers_count,
            'inReplyToStatusId'   => $jsonBody->in_reply_to_status_id_str,
            'inReplyToUserId'     => $jsonBody->in_reply_to_user_id_str,
            'inReplyToScreenName' => $jsonBody->in_reply_to_screen_name,
            'favorited'           => $jsonBody->favorited
        );

        $filter->setData($data);
        $data = $filter->getProcessedData();

        return Conjoon_BeanContext_Inspector::create(
            'Conjoon_Modules_Service_Twitter_Tweet',
            $data
        );
    }

    /**
     * Returns the recent tweets of the user with the speified id.
     *
     * @param array $params A list of parameters to send to the Twitter service
     *
     * @return Conjoon_Error if any error occures, otherwise an array with the
     * Conjoon_Modules_Service_Twitter_Tweet objects
     */
    public function statusesUserTimeline(Array $params = array())
    {
        try {
            $response = $this->_twitter->statusesUserTimeline($params);
        } catch (Zend_Service_Twitter_Exception $e) {
            return $this->fromTwitterServiceException($e);
        }

        if ($response->errors || $response->error) {
            return $this->fromTwitterServiceError($response);
        }

        $entries = array();


        /**
         * @see Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet
         */
        require_once 'Conjoon/Modules/Service/Twitter/Tweet/Filter/Tweet.php';

        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        $filter = new Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet(
            array(), Conjoon_Filter_Input::CONTEXT_RESPONSE
        );


        if (isset($params['user_id'])) {
            $isFollowing = $this->amIFollowingThatUser(array('target_id' => $params['user_id']));
        } else if (isset($params['screen_name'])) {
            $isFollowing = $this->amIFollowingThatUser(array('target_screen_name' => $params['screen_name']));
        } else {
            /**
             * @see Zend_Service_Twitter_Exception
             */
            require_once 'Zend/Service/Twitter/Exception.php';

            throw new Zend_Service_Twitter_Exception("Neither \"user_id\" nor \"screen_name\" was available.");
        }

        if (!is_bool($isFollowing)) {
            return $isFollowing;
        }

        $jsonBody = $response->toValue();

        foreach ($jsonBody as $tweet) {

            $data = array(
                'id'                  => $tweet->id_str,
                'text'                => $tweet->text,
                'createdAt'           => $tweet->created_at,
                'source'              => $tweet->source,
                'truncated'           => $tweet->truncated,
                'userId'              => $tweet->user->id_str,
                'name'                => $tweet->user->name,
                'screenName'          => $tweet->user->screen_name,
                'location'            => $tweet->user->location,
                'profileImageUrl'     => $this->protocolContext === 'https'
                                         ? $tweet->user->profile_image_url_https
                                         : $tweet->user->profile_image_url,
                'url'                 => $tweet->user->url,
                'description'         => $tweet->user->description,
                'protected'           => $tweet->user->protected,
                'isFollowing'         => $isFollowing,
                'followersCount'      => $tweet->user->followers_count,
                'inReplyToStatusId'   => $tweet->in_reply_to_status_id_str,
                'inReplyToUserId'     => $tweet->in_reply_to_user_id_str,
                'inReplyToScreenName' => $tweet->in_reply_to_screen_name,
                'favorited'           => $tweet->favorited
            );

            $filter->setData($data);
            $data = $filter->getProcessedData();

            $entries[] = Conjoon_BeanContext_Inspector::create(
                'Conjoon_Modules_Service_Twitter_Tweet',
                $data
            );
        }

        return $entries;
    }


    /**
     * Retrieves the recent tweets of the users followed by the
     * authenticated user.
     *
     * @return mixed Either an array with the recent tweets, or a Conjoon_Error
     * object
     */
    public function statusesHomeTimeline()
    {
        try {
            $response = $this->_twitter->statusesHomeTimeline();
        } catch (Zend_Service_Twitter_Exception $e) {
            return $this->fromTwitterServiceException($e);
        }

        if ($response->errors || $response->error) {
            return $this->fromTwitterServiceError($response);
        }

        $entries = array();
        $jsonBody = $response->toValue();

        /**
         * @see Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet
         */
        require_once 'Conjoon/Modules/Service/Twitter/Tweet/Filter/Tweet.php';

        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        $filter = new Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet(
            array(), Conjoon_Filter_Input::CONTEXT_RESPONSE
        );

        // in case this is a fresh account and/or no tweets are available,
        // exit here
        if (empty($jsonBody)) {
            return $entries;
        }

        foreach ($jsonBody as $tweet) {
            $data = array(
                'id'                  => $tweet->id_str,
                'text'                => $tweet->text,
                'createdAt'           => $tweet->created_at,
                'source'              => $tweet->source,
                'truncated'           => $tweet->truncated,
                'userId'              => $tweet->user->id_str,
                'name'                => $tweet->user->name,
                'screenName'          => $tweet->user->screen_name,
                'location'            => $tweet->user->location,
                'profileImageUrl'     => $this->protocolContext === 'https'
                                         ? $tweet->user->profile_image_url_https
                                         : $tweet->user->profile_image_url,
                'url'                 => $tweet->user->url,
                'description'         => $tweet->user->description,
                'protected'           => $tweet->user->protected,
                'followersCount'      => $tweet->user->followers_count,
                'isFollowing'         => $tweet->user->following,
                'inReplyToStatusId'   => $tweet->in_reply_to_status_id_str,
                'inReplyToUserId'     => $tweet->in_reply_to_user_id_str,
                'inReplyToScreenName' => $tweet->in_reply_to_screen_name,
                'favorited'           => $tweet->favorited
            );

            $filter->setData($data);
            $data = $filter->getProcessedData();

            $entries[] = Conjoon_BeanContext_Inspector::create(
                'Conjoon_Modules_Service_Twitter_Tweet',
                $data
            );
        }

        return $entries;
    }

    /**
     * Returns true if the user currently signed in with this proxy follows
     * the user specified in target_id or target_screen_name in the passed
     * array.
     *
     * @param array $params an array with options to send to the service. Valid key/value
     * pairs are:
     *  - target_id the id of the target user to check whether this user follows that user
     *   OR
     *  - target_screen_name the screen name of the target user to check whether this user follows that user
     *
     * @return boolean (true/false) or Conjoon_Error
     *
     * @throws InvalidArgumentException if neither target_screen_name or target_id is not set
     */
    public function amIFollowingThatUser(array $params)
    {
        $finParams = array(
            'source_screen_name' => $this->_twitter->getUsername()
        );

        if (isset($params['target_screen_name'])) {
            $finParams['target_screen_name'] = $params['target_screen_name'];
        } else if (isset($params['target_id'])) {
            $finParams['target_id'] = $params['target_id'];
        } else {
            return $this->fromTwitterServiceException(
                new InvalidArgumentException(
                "target_screen_name or target_id missing"
                )
            );
        }

        try {
            $response = $this->_twitter->friendships->show($finParams);
        } catch (Exception $e) {
            return $this->fromTwitterServiceException($e);
        }


        if ($response->errors || $response->error) {
            return $this->fromTwitterServiceError($response);
        }

        $jsonBody = $response->toValue();

        $isFollowing = (bool) $jsonBody->relationship->source->following;

        return $isFollowing;
    }

    /**
     * Update user's current status
     *
     * @param  string $status
     * @param  int $in_reply_to_status_id
     * @return mixed Conjoon_Error on failure, or an Conjoon_Modules_Service_Twitter_Tweet
     * object on success
     */
    public function statusesUpdate($status, $in_reply_to_status_id = null)
    {
        try {
            $response = $this->_twitter->statusesUpdate(
                $status, $in_reply_to_status_id
            );
        } catch (Zend_Service_Twitter_Exception $e) {
            return $this->fromTwitterServiceException($e);
        }

        if ($response->errors || $response->error) {
            return $this->fromTwitterServiceError($response);
        }

        $jsonBody = $response->toValue();

        $data = array(
            'id'                  => $jsonBody->id_str,
            'text'                => $jsonBody->text,
            'createdAt'           => $jsonBody->created_at,
            'source'              => $jsonBody->source,
            'truncated'           => $jsonBody->truncated,
            'userId'              => $jsonBody->user->id_str,
            'name'                => $jsonBody->user->name,
            'screenName'          => $jsonBody->user->screen_name,
            'location'            => $jsonBody->user->location,
            'profileImageUrl'     => $this->protocolContext === 'https'
                                     ? $jsonBody->user->profile_image_url_https
                                     : $jsonBody->user->profile_image_url,
            'url'                 => $jsonBody->user->url,
            'description'         => $jsonBody->user->description,
            'protected'           => $jsonBody->user->protected,
            'followersCount'      => $jsonBody->user->followers_count,
            'isFollowing'         => $jsonBody->user->following,
            'inReplyToStatusId'   => $jsonBody->in_reply_to_status_id_str,
            'inReplyToUserId'     => $jsonBody->in_reply_to_user_id_str,
            'inReplyToScreenName' => $jsonBody->in_reply_to_screen_name,
            'favorited'           => $jsonBody->favorited
        );

        /**
         * @see Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet
         */
        require_once 'Conjoon/Modules/Service/Twitter/Tweet/Filter/Tweet.php';

        $filter = new Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet(
            $data, Conjoon_Filter_Input::CONTEXT_RESPONSE
        );

        $data = $filter->getProcessedData();
        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        $entity = Conjoon_BeanContext_Inspector::create(
            'Conjoon_Modules_Service_Twitter_Tweet',
             $data
        );


        return $entity;
    }

}
