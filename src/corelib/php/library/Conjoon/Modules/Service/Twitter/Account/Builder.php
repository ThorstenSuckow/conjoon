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


class Conjoon_Modules_Service_Twitter_Account_Builder extends Conjoon_Builder {

    protected $_validGetOptions = array('userId');

    /**
     * Returns either a cahced list of twitter accounts for a user, or
     * the accounts out of the database which will immediately be validated
     * against the twitter service (network access)
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - userId: the id of the user to fetch al twitter accounts for
     *
     *
     * @return Array anr array with instances of
     * Conjoon_Modules_Service_Twitter_Account_Model_Account
     */
    protected function _get(Array $options)
    {
        $userId = $options['userId'];

        $cacheId = (string)$userId;

        $cache = $this->_cache;

        if (!($cache->test($cacheId))) {

            /**
             * @see Conjoon_BeanContext_Decorator
             */
            require_once 'Conjoon/BeanContext/Decorator.php';
            $decoratedModel = new Conjoon_BeanContext_Decorator(
                'Conjoon_Modules_Service_Twitter_Account_Model_Account'
            );

            $accounts = $decoratedModel->getAccountsForUserAsDto($userId);

            /**
             * @see Zend_Service_Twitter
             */
            require_once 'Zend/Service/Twitter.php';

            for ($i = 0, $len = count($accounts); $i < $len; $i++) {
                $dto =& $accounts[$i];
                $dto->updateInterval = ((int)$dto->updateInterval) * 1000;

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

            $cache->save($accounts, $cacheId);

        } else {
            $accounts = $cache->load($cacheId);
        }

        return $accounts;
    }

}