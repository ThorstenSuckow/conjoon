<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
     * Returns either a cached list of twitter accounts for a user, or
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

        /**
         * @see Conjoon_Modules_Default_Registry_Facade
         */
        require_once 'Conjoon/Modules/Default/Registry/Facade.php';

        $protocolContext = Conjoon_Modules_Default_Registry_Facade::getInstance()
            ->getValueForKeyAndUserId('/server/environment/protocol', $userId);


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
                $dto->twitterProfileImageUrl = $protocolContext === 'https'
                                               ? $response->profile_image_url_https
                                               : $response->profile_image_url;
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
