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

    protected $_validGetOptions = array('id', 'accountId');

    protected $_validTagOptions = array('accountId');

    protected $_buildClass = 'Conjoon_Modules_Groupware_Feeds_Item_Dto';

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - id: The id of the feed item to return
     */
    protected function _buildId(Array $options)
    {
        return (string)$options['id'];
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
        return array($options['accountId']);
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
        $id        = $options['id'];
        $accountId = $options['accountId'];

        /**
         * @see Conjoon_Modules_Groupware_Feeds_Item_Filter_Item
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Filter/Item.php';

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        // get the account and check whether images are enabled
        $accountModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Feeds_Account_Model_Account'
        );

        $account = $accountModel->getAccountAsDto($accountId);

        $responseType = Conjoon_Modules_Groupware_Feeds_Item_Filter_Item::CONTEXT_ITEM_RESPONSE;

        if ($account->isImageEnabled) {
            $responseType = Conjoon_Modules_Groupware_Feeds_Item_Filter_Item::CONTEXT_ITEM_RESPONSE_IMG;
        }

        $itemResponseFilter = new Conjoon_Modules_Groupware_Feeds_Item_Filter_Item(
            array(),
            $responseType
        );
        $itemModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Feeds_Item_Model_Item',
            $itemResponseFilter
        );

        $item = $itemModel->getItemAsDto($id);

        return $item;
    }

}