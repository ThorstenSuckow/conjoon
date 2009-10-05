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
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @category   Conjoon
 * @package    Conjoon_Controller
 * @subpackage Conjoon_Controller_Action_Helper
 */
class Conjoon_Controller_Action_Helper_FilterRequestData extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var array Conjoon_Filter_Input $_filters
     */
    protected $_filters = array();

    /**
     * @var array $_filterKeys
     */
    protected $_filterKeys = array();

    protected function _getFilter($key)
    {
        if (isset($this->_filters[$key])) {
            return $this->_filters[$key];
        }

        switch ($key) {

            case 'Groupware_FeedsItemController::get.feed.items':
                /**
                 * @see Conjoon_Modules_Groupware_Feeds_Item_Filter_Item
                 */
                require_once 'Conjoon/Modules/Groupware/Feeds/Item/Filter/Item.php';

                $this->_filters[$key] = new Conjoon_Modules_Groupware_Feeds_Item_Filter_Item(
                    array(),
                    Conjoon_Filter_Input::CONTEXT_UPDATE
                );
            break;

            case 'Groupware_FeedsItemController::set.item.read':
                /**
                 * @see Conjoon_Modules_Groupware_Feeds_Item_Filter_Item
                 */
                require_once 'Conjoon/Modules/Groupware/Feeds/Item/Filter/Item.php';

                $this->_filters[$key] = new Conjoon_Modules_Groupware_Feeds_Item_Filter_Item(
                    array(),
                    Conjoon_Modules_Groupware_Feeds_Item_Filter_Item::CONTEXT_READ
                );
            break;

            case 'Groupware_FeedsItemController::get.feed.content':
                /**
                 * @see Conjoon_Modules_Groupware_Feeds_Item_Filter_Item
                 */
                require_once 'Conjoon/Modules/Groupware/Feeds/Item/Filter/Item.php';

                $this->_filters[$key] = new Conjoon_Modules_Groupware_Feeds_Item_Filter_Item(
                    array(),
                    Conjoon_Modules_Groupware_Feeds_Item_Filter_Item::CONTEXT_ITEM_CONTENT
                );
            break;

            case 'Groupware_FeedsAccountController::is.feed.address.valid':
                /**
                 * @see Conjoon_Modules_Groupware_Feeds_Item_Filter_Item
                 */
                require_once 'Conjoon/Modules/Groupware/Feeds/Item/Filter/Item.php';

                $this->_filters[$key] = new Conjoon_Modules_Groupware_Feeds_Item_Filter_Item(
                    array(),
                    Conjoon_Modules_Groupware_Feeds_Item_Filter_Item::CONTEXT_URI_CHECK
                );
            break;

            case 'Groupware_FeedsAccountController::update.accounts':
                /**
                 * @see Conjoon_Modules_Groupware_Feeds_Account_Filter_Account
                 */
                require_once 'Conjoon/Modules/Groupware/Feeds/Account/Filter/Account.php';

                $this->_filters[$key] = new Conjoon_Modules_Groupware_Feeds_Account_Filter_Account(
                    array(),
                    Conjoon_Modules_Groupware_Feeds_Account_Filter_Account::CONTEXT_EXTRACT_UPDATE
                );
            break;

            case 'Groupware_FeedsAccountController::add.feed':
                /**
                 * @see Conjoon_Modules_Groupware_Feeds_Account_Filter_Account
                 */
                require_once 'Conjoon/Modules/Groupware/Feeds/Account/Filter/Account.php';

                $this->_filters[$key] = new Conjoon_Modules_Groupware_Feeds_Account_Filter_Account(
                    array(),
                    Conjoon_Modules_Groupware_Feeds_Account_Filter_Account::CONTEXT_CREATE
                );
            break;
        }

        return $this->_filters[$key];
    }


    public function registerFilter($key)
    {
        $exp = explode('::', $key);

        $class  = $exp[0];
        $action = $exp[1];

        $thisClass = get_class($this->getActionController());

        if ($thisClass != $class) {
            throw new Zend_Controller_Action_Exception(
                "class for filter is not this controller: \"$thisClass\" \"$class\""
            );
        }

        $this->_filterKeys[$thisClass][$action] = $key;

        return $this;
    }

    public function preDispatch()
    {
        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        $class  = get_class($this->getActionController());

        if (!isset($this->_filterKeys[$class][$action])) {
            return;
        }

        $filter = $this->_getFilter($this->_filterKeys[$class][$action]);

        $actionController = $this->getActionController();

        $filter->setData($this->getRequest()->getParams());

        $filteredData = $filter->getProcessedData();

        foreach ($filteredData as $key => $value) {
            $this->getRequest()->setParam($key, $value);
        }
    }

    /**
     *
     * @return Conjoon_Controller_Action_Helper_FilterRequestData
     */
    public function direct()
    {
        return $this;
    }

}
