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
            $dto->updateInterval = ((int)$dto->updateInterval) * 1000;

            try {
                /**
                 * @todo move to separate model
                 */
                $twitter = new Conjoon_Service_Twitter($dto->name, $dto->password);
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
                Conjoon_Log::log(
                    "Could not retrieve account information for twitter "
                    . "account: \"".$e->getMessage()."\"",
                    Zend_Log::INFO
                );
                // ignore
            }

            $dto->password = str_pad("", strlen($dto->password), '*');
        }

        return $accounts;
    }

}