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
 * A factory for creating various builders.
 *
 * @category   Conjoon
 * @package    Conjoon
 * @subpackage Builder
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Builder_Factory {

    /**
     * Convinient method to create and return objects of the type Conjoon_Builder.
     *
     *
     * @param string $key The key used to determine which builder class to return
     * @param array $options A set of options with which the needed cache objects
     * can be created. See Conjoon_Cache_Factory
     * @param Conjoon_BeanContext_Decoratable
     *
     * @return Conjoon_Builder
     */
    public static function getBuilder($key, Array $options, Conjoon_BeanContext_Decoratable $model = null)
    {
        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        /**
         * @see Conjoon_Cache_Factory
         */
        require_once 'Conjoon/Cache/Factory.php';

        $cache = Conjoon_Cache_Factory::getCache($key, $options);

        switch ($key) {
            case Conjoon_Keys::CACHE_EMAIL_MESSAGE:

                /**
                 * @see Conjoon_Modules_Groupware_Email_Message_Builder
                 */
                require_once 'Conjoon/Modules/Groupware/Email/Message/Builder.php';

                return new Conjoon_Modules_Groupware_Email_Message_Builder($cache, $model);

            break;

            case Conjoon_Keys::CACHE_EMAIL_ACCOUNTS:

                /**
                 * @see Conjoon_Modules_Groupware_Email_Account_Builder
                 */
                require_once 'Conjoon/Modules/Groupware/Email/Account/Builder.php';

                return new Conjoon_Modules_Groupware_Email_Account_Builder($cache, $model);

            break;

            case Conjoon_Keys::CACHE_FEED_ITEM:

                /**
                 * @see Conjoon_Modules_Groupware_Feeds_Item_Builder
                 */
                require_once 'Conjoon/Modules/Groupware/Feeds/Item/Builder.php';

                return new Conjoon_Modules_Groupware_Feeds_Item_Builder($cache, $model);

            break;

            case Conjoon_Keys::CACHE_FEED_ITEMLIST:

                /**
                 * @see Conjoon_Modules_Groupware_Feeds_Item_ListBuilder
                 */
                require_once 'Conjoon/Modules/Groupware/Feeds/Item/ListBuilder.php';

                return new Conjoon_Modules_Groupware_Feeds_Item_ListBuilder($cache, $model);

            break;

            case Conjoon_Keys::CACHE_FEED_ACCOUNT:

                /**
                 * @see Conjoon_Modules_Groupware_Feeds_Account_Builder
                 */
                require_once 'Conjoon/Modules/Groupware/Feeds/Account/Builder.php';

                return new Conjoon_Modules_Groupware_Feeds_Account_Builder($cache, $model);

            break;

            case Conjoon_Keys::CACHE_FEED_ACCOUNTLIST:

                /**
                 * @see Conjoon_Modules_Groupware_Feeds_Account_ListBuilder
                 */
                require_once 'Conjoon/Modules/Groupware/Feeds/Account/ListBuilder.php';

                return new Conjoon_Modules_Groupware_Feeds_Account_ListBuilder($cache, $model);

            break;

            case Conjoon_Keys::CACHE_TWITTER_ACCOUNTS:

                /**
                 * @see Conjoon_Modules_Service_Twitter_Account_Builder
                 */
                require_once 'Conjoon/Modules/Service/Twitter/Account/Builder.php';

                return new Conjoon_Modules_Service_Twitter_Account_Builder($cache, $model);

            break;
        }


    }


}