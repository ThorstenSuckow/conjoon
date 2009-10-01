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
     *
     * @return Conjoon_Modules_Groupware_Feeds_Item_Dto
     */
    protected function _build(Array $options)
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
        $itemModel = new Conjoon_BeanContext_Decorator(
            $this->getModel(),
            $itemResponseFilter
        );

        return $itemModel->getItemsForAccountAsDto($accountId);
    }

}