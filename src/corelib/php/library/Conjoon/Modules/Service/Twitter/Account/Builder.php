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
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * @see Conjoon_Builder
 */
require_once 'Conjoon/Builder.php';

/**
 * @see Conjoon_Log
 */
require_once 'Conjoon/Log.php';


class Conjoon_Modules_Service_Twitter_Account_Builder extends Conjoon_Builder {

    protected $_validGetOptions = array('userId');

    protected $_buildClass = 'Conjoon_Modules_Service_Twitter_Account_Dto';

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - userId: the id of the user to fetch al twitter accounts for
     */
    protected function _buildId(Array $options)
    {
        return (string)$options['userId'];
    }

   /**
     * @return Conjoon_Modules_Service_Twitter_Account_Model_Account
     */
    protected function _getModel()
    {
        /**
         * @see Conjoon_Modules_Service_Twitter_Account_Model_Account
         */
        require_once 'Conjoon/Modules/Service/Twitter/Account/Model/Account.php';

        return new Conjoon_Modules_Service_Twitter_Account_Model_Account();
    }

    /**
     * Returns either a cahced list of twitter accounts for a user, or
     * the accounts out of the database which will immediately be validated
     * against the twitter service (network access)
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - userId: the id of the user to fetch al twitter accounts for
     * @param Conjoon_BeanContext_Decoratable $model
     *
     * @return Array anr array with instances of
     * Conjoon_Modules_Service_Twitter_Account_Model_Account
     */
    protected function _build(Array $options, Conjoon_BeanContext_Decoratable $model)
    {
        $userId = $options['userId'];

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedModel = new Conjoon_BeanContext_Decorator($model);

        $accounts = $decoratedModel->getAccountsForUserAsDto($userId);

        /**
         * @see Conjoon_Service_Twitter
         */
        require_once 'Conjoon/Service/Twitter.php';

        for ($i = 0, $len = count($accounts); $i < $len; $i++) {
            $dto =& $accounts[$i];

            try {
                /**
                 * @todo move to separate model
                 */

                /**
                 * @see Zend_Oauth_Token_Access
                 */
                require_once 'Zend/Oauth/Token/Access.php';

                $accessToken = new Zend_Oauth_Token_Access();
                $accessToken->setParams(array(
                    'oauth_token'        => $dto->oauthToken,
                    'oauth_token_secret' => $dto->oauthTokenSecret,
                    'user_id'            => $dto->twitterId,
                    'screen_name'        => $dto->name
                ));

                $twitter = new Conjoon_Service_Twitter(array(
                    'username'    => $dto->name,
                    'accessToken' => $accessToken
                ));

                $response = $twitter->userShow($dto->name);

                $dto->twitterId              = $response->id_str;
                $dto->twitterName            = $response->name;
                $dto->twitterScreenName      = $response->screen_name;
                $dto->twitterLocation        = $response->location;
                $dto->twitterProfileImageUrl = $response->profile_image_url;
                $dto->twitterUrl             = $response->url;
                $dto->twitterProtected       = $response->protected;
                $dto->twitterDescription     = $response->description;
                $dto->twitterFollowersCount  = $response->followers_count;

            } catch (Exception $e) {
                Conjoon_Log::log(
                    "Could not retrieve account information for twitter "
                    . "account: \"".$e->getMessage()."\"",
                    Zend_Log::INFO
                );
                // ignore
            }

            $dto->oauthTokenSecret = str_pad("", strlen($dto->oauthTokenSecret), '*');
        }

        return $accounts;
    }

}
