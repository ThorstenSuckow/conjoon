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


class Conjoon_Modules_Groupware_Feeds_Item_Builder extends Conjoon_Builder {

    protected $_validGetOptions = array('id', 'accountId', 'isImageEnabled');

    protected $_validTagOptions = array('accountId');

    protected $_buildClass = 'Conjoon_Modules_Groupware_Feeds_Item_Dto';

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - id: The id of the feed item to return
     *   - isImageEnabled: whether or not images for the account for this
     *     feed item are enabled
     */
    protected function _buildId(Array $options)
    {
        return ((string)$options['id'])
               . '_'
               . ($options['isImageEnabled'] ? "1" : "0");
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
        return array((string)$options['accountId']);
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
        $id             = $options['id'];
        $accountId      = $options['accountId'];
        $isImageEnabled = $options['isImageEnabled'];

        /**
         * @see Conjoon_Modules_Groupware_Feeds_Item_Filter_Item
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Filter/Item.php';

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        $responseType = Conjoon_Modules_Groupware_Feeds_Item_Filter_Item::CONTEXT_ITEM_RESPONSE;

        if ($isImageEnabled) {
            $responseType = Conjoon_Modules_Groupware_Feeds_Item_Filter_Item::CONTEXT_ITEM_RESPONSE_IMG;
        }

        $itemResponseFilter = new Conjoon_Modules_Groupware_Feeds_Item_Filter_Item(
            array(),
            $responseType
        );

        $itemModel = new Conjoon_BeanContext_Decorator(
            $model,
            $itemResponseFilter
        );

        $item = $itemModel->getItemAsDto($id);

        return $item;
    }

}