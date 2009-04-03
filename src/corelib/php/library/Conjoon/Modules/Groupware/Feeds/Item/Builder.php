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


class Conjoon_Modules_Groupware_Feeds_Item_Builder extends Conjoon_Builder {

    protected $_validGetOptions = array('id');

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - id: The id of the feed item to return
     *
     * @return Conjoon_Modules_Groupware_Feeds_Item_Dto
     */
    protected function _get(Array $options)
    {
        $id = $options['id'];

        $cacheId = (string)$id;

        $cache = $this->_cache;

        if (!($cache->test($cacheId))) {

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
                Conjoon_Modules_Groupware_Feeds_Item_Filter_Item::CONTEXT_ITEM_RESPONSE
            );
            $itemModel = new Conjoon_BeanContext_Decorator(
                'Conjoon_Modules_Groupware_Feeds_Item_Model_Item',
                $itemResponseFilter
            );

            $item = $itemModel->getItemAsDto($id);

            $cache->save($item, $cacheId);

        } else {
            $item = $cache->load($cacheId);
        }

        return $item;
    }

}