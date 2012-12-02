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
     * Creates a new instance of Conjoon_Service_Twitter_Proxy
     *
     * @param array|Zend_Config a configuration object for this class, with at
     * least the following key/values:
     * - oauth_token - as provided by Twitter's oauth
     * - oauth_token_secret - as provided by Twitter's oauth
     * - user_id - id of the user as provided by the twitter service
     * - screen_name - screen name of the user as provided by the twitter
     *                 service
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
            'oauth_token', 'oauth_token_secret', 'user_id', 'screen_name'
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

        /**
         * @see Zend_Oauth_Token_Access
         */
        require_once 'Zend/Oauth/Token/Access.php';

        $accessToken = new Zend_Oauth_Token_Access();
        $accessToken->setParams($accessTokenOptions);

        $this->_twitter = new Conjoon_Service_Twitter(array(
            'username'    => $accessTokenOptions['screen_name'],
            'accessToken' => $accessToken
        ));
    }

    /**
     * End current session
     *
     * @return true
     */
    public function accountEndSession()
    {
        return $this->_twitter->accountEndSession();
    }

    /**
     * Verifies account credentials.
     *
     * @return true or Conjoon_Modules_Service_Twitter_Account_Dto
     */
    public function accountVerifyCredentials()
    {
        try {
            /**
             * @ticket CN-675
             */
            $response = $this->_twitter->userShow(
                $this->_twitter->getUsername()
            );

            //$response = $this->_twitter->accountVerifyCredentials();
        } catch (Zend_Service_Twitter_Exception $e) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                $e->getMessage(), Conjoon_Error::LEVEL_ERROR
            );
        }

        if (isset($response->error)) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                (string)$response->error .
                " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
        }

        /**
         * @see Conjoon_Modules_Service_Twitter_Account_Dto
         */
        require_once 'Conjoon/Modules/Service/Twitter/Account/Dto.php';

        $dto = new Conjoon_Modules_Service_Twitter_Account_Dto;

        $dto->twitterId              = (string)$response->id;
        $dto->twitterName            = (string)$response->name;
        $dto->twitterScreenName      = (string)$response->screen_name;
        $dto->twitterLocation        = (string)$response->location;
        $dto->twitterProfileImageUrl = (string)$response->profile_image_url;
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
    public function friendshipCreate($id)
    {
        try {
            $response = $this->_twitter->friendshipCreate($id);
        } catch (Zend_Service_Twitter_Exception $e) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                $e->getMessage(), Conjoon_Error::LEVEL_ERROR
            );
        }

        if (isset($response->error)) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                (string)$response->error .
                " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
        }

        return true;
    }

    /**
     * Destroy friendship
     *
     * @param  int|string $id User ID or name of friend to remove
     * @return true or Conjoon_Error
     */
    public function friendshipDestroy($id)
    {
        try {
            $response = $this->_twitter->friendshipDestroy($id);
        } catch (Zend_Service_Twitter_Exception $e) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                $e->getMessage(), Conjoon_Error::LEVEL_ERROR
            );
        }

        if (isset($response->error)) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                (string)$response->error .
                " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
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
                $favoriteStatus = $this->_twitter->favoriteCreate($id);
            } else {
                $favoriteStatus = $this->_twitter->favoriteDestroy($id);
            }
        } catch (Zend_Service_Twitter_Exception $e) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                $e->getMessage(), Conjoon_Error::LEVEL_ERROR
            );
        }

        if (isset($favoriteStatus->error)) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                (string)$favoriteStatus->error .
                " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
        }

        $data = array(
            'id'                  => (string)$favoriteStatus->id,
            'text'                => (string)$favoriteStatus->text,
            'createdAt'           => (string)$favoriteStatus->created_at,
            'source'              => (string)$favoriteStatus->source,
            'truncated'           => (string)$favoriteStatus->truncated,
            'userId'              => (string)$favoriteStatus->user->id,
            'name'                => (string)$favoriteStatus->user->name,
            'screenName'          => (string)$favoriteStatus->user->screen_name,
            'location'            => (string)$favoriteStatus->user->location,
            'profileImageUrl'     => (string)$favoriteStatus->user->profile_image_url,
            'url'                 => (string)$favoriteStatus->user->url,
            'description'         => (string)$favoriteStatus->user->description,
            'protected'           => (string)$favoriteStatus->user->protected,
            'isFollowing'         => (string)$favoriteStatus->user->following,
            'followersCount'      => (string)$favoriteStatus->user->followers_count,
            'inReplyToStatusId'   => (string)$favoriteStatus->in_reply_to_status_id,
            'inReplyToUserId'     => (string)$favoriteStatus->in_reply_to_user_id,
            'inReplyToScreenName' => (string)$favoriteStatus->in_reply_to_screen_name,
            'favorited'           => $favorite//(string)$favoriteStatus->favorited
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
            $destroyStatus = $this->_twitter->statusDestroy($id);
        } catch (Zend_Service_Twitter_Exception $e) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                $e->getMessage(), Conjoon_Error::LEVEL_ERROR
            );
        }

        if (isset($destroyStatus->error)) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                (string)$destroyStatus->error .
                " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
        }

        $data = array(
            'id'                  => (string)$destroyStatus->id,
            'text'                => (string)$destroyStatus->text,
            'createdAt'           => (string)$destroyStatus->created_at,
            'source'              => (string)$destroyStatus->source,
            'truncated'           => (string)$destroyStatus->truncated,
            'userId'              => (string)$destroyStatus->user->id,
            'name'                => (string)$destroyStatus->user->name,
            'screenName'          => (string)$destroyStatus->user->screen_name,
            'location'            => (string)$destroyStatus->user->location,
            'profileImageUrl'     => (string)$destroyStatus->user->profile_image_url,
            'url'                 => (string)$destroyStatus->user->url,
            'description'         => (string)$destroyStatus->user->description,
            'protected'           => (string)$destroyStatus->user->protected,
            'isFollowing'         => (string)$destroyStatus->user->following,
            'followersCount'      => (string)$destroyStatus->user->followers_count,
            'inReplyToStatusId'   => (string)$destroyStatus->in_reply_to_status_id,
            'inReplyToUserId'     => (string)$destroyStatus->in_reply_to_user_id,
            'inReplyToScreenName' => (string)$destroyStatus->in_reply_to_screen_name,
            'favorited'           => (string)$destroyStatus->favorited
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
    public function statusShow($id)
    {
        try {
            $tweet = $this->_twitter->statusShow($id);
        } catch (Zend_Service_Twitter_Exception $e) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                $e->getMessage(), Conjoon_Error::LEVEL_ERROR
            );
        }

        if (isset($tweet->error)) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                (string)$tweet->error .
                " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
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

        $tweetUserId = (string)$tweet->user->id;

        $isFollowing = $this->friendshipExists($tweetUserId);

        if (!is_bool($isFollowing)) {
            return $isFollowing;
        }

        $data = array(
            'id'                  => (string)$tweet->id,
            'text'                => (string)$tweet->text,
            'createdAt'           => (string)$tweet->created_at,
            'source'              => (string)$tweet->source,
            'truncated'           => (string)$tweet->truncated,
            'userId'              => $tweetUserId,
            'name'                => (string)$tweet->user->name,
            'screenName'          => (string)$tweet->user->screen_name,
            'location'            => (string)$tweet->user->location,
            'profileImageUrl'     => (string)$tweet->user->profile_image_url,
            'url'                 => (string)$tweet->user->url,
            'description'         => (string)$tweet->user->description,
            'protected'           => (string)$tweet->user->protected,
            'isFollowing'         => $isFollowing,
            'followersCount'      => (string)$tweet->user->followers_count,
            'inReplyToStatusId'   => (string)$tweet->in_reply_to_status_id,
            'inReplyToUserId'     => (string)$tweet->in_reply_to_user_id,
            'inReplyToScreenName' => (string)$tweet->in_reply_to_screen_name,
            'favorited'           => (string)$tweet->favorited
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
    public function statusUserTimeline(Array $params = array())
    {
        try {
            $tweets = $this->_twitter->statusUserTimeline($params);
        } catch (Zend_Service_Twitter_Exception $e) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                $e->getMessage(), Conjoon_Error::LEVEL_ERROR
            );
        }

        if (isset($tweets->error)) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                (string)$tweets->error .
                " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
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


        if (isset($params['id'])) {
            $isFollowing = $this->friendshipExists($params['id']);
        } else if (isset($params['screen_name'])) {
                $isFollowing = $this->friendshipExists($params['screen_name']);
        } else {
            throw new Zend_Service_Twitter_Exception("Neither \"id\" nor \"screen_name\" was available.");
        }

        if (!is_bool($isFollowing)) {
            return $isFollowing;
        }

        if (!$tweets->status) {
            return array();
        }

        foreach ($tweets->status as $tweet) {
            $data = array(
                'id'                  => (string)$tweet->id,
                'text'                => (string)$tweet->text,
                'createdAt'           => (string)$tweet->created_at,
                'source'              => (string)$tweet->source,
                'truncated'           => (string)$tweet->truncated,
                'userId'              => (string)$tweet->user->id,
                'name'                => (string)$tweet->user->name,
                'screenName'          => (string)$tweet->user->screen_name,
                'location'            => (string)$tweet->user->location,
                'profileImageUrl'     => (string)$tweet->user->profile_image_url,
                'url'                 => (string)$tweet->user->url,
                'description'         => (string)$tweet->user->description,
                'protected'           => (string)$tweet->user->protected,
                'isFollowing'         => $isFollowing,
                'followersCount'      => (string)$tweet->user->followers_count,
                'inReplyToStatusId'   => (string)$tweet->in_reply_to_status_id,
                'inReplyToUserId'     => (string)$tweet->in_reply_to_user_id,
                'inReplyToScreenName' => (string)$tweet->in_reply_to_screen_name,
                'favorited'           => (string)$tweet->favorited
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
    public function statusFriendsTimeline()
    {
        try {
            $tweets = $this->_twitter->statusFriendsTimeline();
        } catch (Zend_Service_Twitter_Exception $e) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                $e->getMessage(), Conjoon_Error::LEVEL_ERROR
            );
        }

        if (isset($tweets->error)) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                (string)$tweets->error .
                " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
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

        // in case this is a fresh account and/or no tweets are available,
        // exit here
        if (!$tweets->status) {
            return $entries;
        }

        foreach ($tweets->status as $tweet) {
            $data = array(
                'id'                  => (string)$tweet->id,
                'text'                => (string)$tweet->text,
                'createdAt'           => (string)$tweet->created_at,
                'source'              => (string)$tweet->source,
                'truncated'           => (string)$tweet->truncated,
                'userId'              => (string)$tweet->user->id,
                'name'                => (string)$tweet->user->name,
                'screenName'          => (string)$tweet->user->screen_name,
                'location'            => (string)$tweet->user->location,
                'profileImageUrl'     => (string)$tweet->user->profile_image_url,
                'url'                 => (string)$tweet->user->url,
                'description'         => (string)$tweet->user->description,
                'protected'           => (string)$tweet->user->protected,
                'followersCount'      => (string)$tweet->user->followers_count,
                'isFollowing'         => (string)$tweet->user->following,
                'inReplyToStatusId'   => (string)$tweet->in_reply_to_status_id,
                'inReplyToUserId'     => (string)$tweet->in_reply_to_user_id,
                'inReplyToScreenName' => (string)$tweet->in_reply_to_screen_name,
                'favorited'           => (string)$tweet->favorited
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
     * Returns true if userA follows userB, otherwise false.
     *
     * @param  mixed $userId either the screenName or the id of the user to
     * check the friendship against

     *
     * @return boolean (true/false) or Conjoon_Error
     */
    public function friendshipExists($userId)
    {
        try {
            $tweets = $this->_twitter->friendshipExists($userId);
        } catch (Exception $e) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                $e->getMessage(), Conjoon_Error::LEVEL_ERROR
            );
        }


        if (isset($tweets->error)) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                (string)$tweets->error .
                " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
        }

        $isFollowing = (string)$tweets->friends;

        if ($isFollowing === "true") {
            return true;
        }

        return false;
    }

    /**
     * Update user's current status
     *
     * @param  string $status
     * @param  int $in_reply_to_status_id
     * @return mixed Conjoon_Error on failure, or an Conjoon_Modules_Service_Twitter_Tweet
     * object on success
     */
    public function statusUpdate($status, $in_reply_to_status_id = null)
    {
        try {
            $tweet = $this->_twitter->statusUpdate(
                $status, $in_reply_to_status_id
            );
        } catch (Zend_Service_Twitter_Exception $e) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                $e->getMessage(), Conjoon_Error::LEVEL_ERROR
            );
        }

        if (isset($tweet->error)) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            return Conjoon_Error_Factory::createError(
                (string)$tweet->error .
                " [username: \"" .$this->_twitter->getUsername() . "\"]",
                Conjoon_Error::LEVEL_ERROR
            );
        }

        $data = array(
            'id'                  => (string)$tweet->id,
            'text'                => (string)$tweet->text,
            'createdAt'           => (string)$tweet->created_at,
            'source'              => (string)$tweet->source,
            'truncated'           => (string)$tweet->truncated,
            'userId'              => (string)$tweet->user->id,
            'name'                => (string)$tweet->user->name,
            'screenName'          => (string)$tweet->user->screen_name,
            'location'            => (string)$tweet->user->location,
            'profileImageUrl'     => (string)$tweet->user->profile_image_url,
            'url'                 => (string)$tweet->user->url,
            'description'         => (string)$tweet->user->description,
            'protected'           => (string)$tweet->user->protected,
            'followersCount'      => (string)$tweet->user->followers_count,
            'isFollowing'         => (string)$tweet->user->following,
            'inReplyToStatusId'   => (string)$tweet->in_reply_to_status_id,
            'inReplyToUserId'     => (string)$tweet->in_reply_to_user_id,
            'inReplyToScreenName' => (string)$tweet->in_reply_to_screen_name,
            'favorited'           => (string)$tweet->favorited
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