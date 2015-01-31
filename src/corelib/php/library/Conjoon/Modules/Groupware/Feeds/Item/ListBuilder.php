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


class Conjoon_Modules_Groupware_Feeds_Item_ListBuilder extends Conjoon_Builder {

    protected $_validGetOptions = array('accountId');

    protected $_buildClass = 'Conjoon_Modules_Groupware_Feeds_Item_Dto';

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - accountId: The accountId for which the items should be returned
     */
    protected function _buildId(Array $options)
    {
        return (string)$options['accountId'];
    }

    /**
     * @return Conjoon_Modules_Groupware_Feeds_Item_Model_Item
     */
    protected function _getModel()
    {
        /**
         * @see Conjoon_Modules_Groupware_Feeds_Item_Model_Item
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Model/Item.php';

        return new Conjoon_Modules_Groupware_Feeds_Item_Model_Item();
    }

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - id: The id of the feed item to return
     * @param Conjoon_BeanContext_Decoratable $model
     *
     * @return Conjoon_Modules_Groupware_Feeds_Item_Dto
     */
    protected function _build(Array $options, Conjoon_BeanContext_Decoratable $model)
    {
        $accountId = $options['accountId'];

        /**
         * @see Conjoon_Modules_Groupware_Feeds_Item_Filter_Item
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Filter/Item.php';

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        $itemResponseFilter = new Conjoon_Modules_Groupware_Feeds_Item_Filter_Item(
            array(),
            Conjoon_Filter_Input::CONTEXT_RESPONSE
        );
        $itemModel = new Conjoon_BeanContext_Decorator($model, $itemResponseFilter);

        return $itemModel->getItemsForAccountAsDto($accountId);
    }

}