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


class Conjoon_Modules_Groupware_Feeds_Account_ListBuilder extends Conjoon_Builder {

    protected $_validGetOptions = array('userId');

    protected $_buildClass = 'Conjoon_Modules_Groupware_Feeds_Account_Dto';

    protected $_validTagOptions = array('userId');

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
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - accountId: The id of the related account this feed was retrieved
     * for
     */
    protected function _getTagList(Array $options)
    {
        return array((string)$options['userId']);
    }

    /**
     * @return Conjoon_Modules_Groupware_Feeds_Account_Model_Account
     */
    protected function _getModel()
    {
        /**
         * @see Conjoon_Modules_Groupware_Feeds_Account_Model_Account
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Account/Model/Account.php';

        return new Conjoon_Modules_Groupware_Feeds_Account_Model_Account();
    }

    /**
     * Returns either a cached list of feed accounts for a user, or
     * the accounts out of the database.
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - userId: the id of the user to fetch all feed accounts for
     * @param Conjoon_BeanContext_Decoratable $model
     *
     * @return Array an array with instances of
     * Conjoon_Modules_Groupware_Feeds_Account_Model_Account_Dto
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

        return $accounts;
    }

}