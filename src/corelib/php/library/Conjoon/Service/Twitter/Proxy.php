<?php


require_once 'Conjoon/Service/Twitter.php';

class Conjoon_Service_Twitter_Proxy  {


    /**
     * @var Conjoon_Service_Twitter
     */
    private $_twitter;

    public function __construct($username, $password)
    {
        $this->_twitter = new Conjoon_Service_Twitter($username, $password);
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
     * Favorites or unfavorites a tweet based on the $favorite
     * parameter.
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
                " [username: \"" .$this->_twitter->getUsername() . "\"; ".
                " using password: " . ($this->_twitter->getPassword() != null ? "yes" : "no") .
                "]",
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
            'followersCount'      => (string)$favoriteStatus->user->followers_count,
            'inReplyToStatusId'   => (string)$favoriteStatus->in_reply_to_status_id,
            'inReplyToUserId'     => (string)$favoriteStatus->in_reply_to_user_id,
            'inReplyToScreenName' => (string)$favoriteStatus->in_reply_to_screen_name,
            'favorited'           => (string)$favoriteStatus->favorited
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
                " [username: \"" .$this->_twitter->getUsername() . "\"; ".
                " using password: " . ($this->_twitter->getPassword() != null ? "yes" : "no") .
                "]",
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
                " [username: \"" .$this->_twitter->getUsername() . "\"; ".
                " using password: " . ($this->_twitter->getPassword() != null ? "yes" : "no") .
                "]",
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
                " [username: \"" .$this->_twitter->getUsername() . "\"; ".
                " using password: " . ($this->_twitter->getPassword() != null ? "yes" : "no") .
                "]",
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
                " [username: \"" .$this->_twitter->getUsername() . "\"; ".
                " using password: " . ($this->_twitter->getPassword() != null ? "yes" : "no") .
                "]",
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