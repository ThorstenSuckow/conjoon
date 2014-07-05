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
 * A factory for creating various builders.
 *
 * @category   Conjoon
 * @package    Conjoon
 * @subpackage Builder
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
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

            case Conjoon_Keys::CACHE_EMAIL_FOLDERS_ROOT_TYPE:

                /**
                 * @see Conjoon_Modules_Groupware_Email_Folder_FolderRootTypeBuilder
                 */
                require_once 'Conjoon/Modules/Groupware/Email/Folder/FolderRootTypeBuilder.php';

                return new Conjoon_Modules_Groupware_Email_Folder_FolderRootTypeBuilder($cache, $model);

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