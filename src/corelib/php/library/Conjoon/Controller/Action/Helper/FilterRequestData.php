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

    /**
     * @var array $_extractFromExtDirect
     */
    protected $_extractFromExtDirect = array();

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

            case 'Service_TwitterAccountController::add.account':
                /**
                 * @see Conjoon_Modules_Service_Twitter_Account_Filter_Account
                 */
                require_once 'Conjoon/Modules/Service/Twitter/Account/Filter/Account.php';

                $this->_filters[$key] = new Conjoon_Modules_Service_Twitter_Account_Filter_Account(
                    array(),
                    Conjoon_Modules_Service_Twitter_Account_Filter_Account::CONTEXT_CREATE
                );
            break;

            case 'Service_TwitterAccountController::remove.account':
                /**
                 * @see Conjoon_Modules_Service_Twitter_Account_Filter_Account
                 */
                require_once 'Conjoon/Modules/Service/Twitter/Account/Filter/Account.php';

                $this->_filters[$key] = new Conjoon_Modules_Service_Twitter_Account_Filter_Account(
                    array(),
                    Conjoon_Modules_Service_Twitter_Account_Filter_Account::CONTEXT_DELETE
                );
            break;

            case 'Service_TwitterAccountController::update.account':
                /**
                 * @see Conjoon_Modules_Service_Twitter_Account_Filter_Account
                 */
                require_once 'Conjoon/Modules/Service/Twitter/Account/Filter/Account.php';

                $this->_filters[$key] = new Conjoon_Modules_Service_Twitter_Account_Filter_Account(
                    array(),
                    Conjoon_Modules_Service_Twitter_Account_Filter_Account::CONTEXT_UPDATE_REQUEST
                );
            break;

            case 'Groupware_FileController::download.file':
                /**
                 * @see Conjoon_Modules_Groupware_Files_File_Filter_File
                 */
                require_once 'Conjoon/Modules/Groupware/Files/File/Filter/File.php';

                $this->_filters[$key] = new Conjoon_Modules_Groupware_Files_File_Filter_File(
                    array(),
                    Conjoon_Modules_Groupware_Files_File_Filter_File::CONTEXT_DOWNLOAD_REQUEST
                );
            break;

            case 'Groupware_EmailFolderController::rename.folder':
                /**
                 * @see Conjoon_Modules_Groupware_Email_Folder_Filter_Folder
                 */
                require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
                $this->_filters[$key] = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
                    array(),
                    Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_RENAME
                );
            break;

            case 'Groupware_EmailFolderController::move.folder':
                /**
                 * @see Conjoon_Modules_Groupware_Email_Folder_Filter_Folder
                 */
                require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
                $this->_filters[$key] = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
                    array(),
                    Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_MOVE
                );
            break;

            case 'Groupware_EmailFolderController::get.folder':
                /**
                 * @see Conjoon_Modules_Groupware_Email_Folder_Filter_Folder
                 */
                require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
                $this->_filters[$key] = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
                    array(),
                    Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_RESPONSE
                );
            break;

            case 'Groupware_EmailFolderController::add.folder':
                /**
                 * @see Conjoon_Modules_Groupware_Email_Folder_Filter_Folder
                 */
                require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
                $this->_filters[$key] = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
                    array(),
                    Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_CREATE
                );
            break;

            case 'RegistryController::set.entries':
                /**
                 * @see Conjoon_Modules_Default_Registry_Filter_Registry
                 */
                require_once 'Conjoon/Modules/Default/Registry/Filter/Registry.php';
                $this->_filters[$key] = new Conjoon_Modules_Default_Registry_Filter_Registry (
                    array(),
                    Conjoon_Modules_Default_Registry_Filter_Registry::CONTEXT_UPDATE_REQUEST
                );
            break;

            case 'ApplicationCacheController::set.clear.flag':
                /**
                 * @see Conjoon_Modules_Default_ApplicationCache_Filter
                 */
                require_once 'Conjoon/Modules/Default/ApplicationCache/Filter.php';
                $this->_filters[$key] = new Conjoon_Modules_Default_ApplicationCache_Filter (
                    array(),
                    Conjoon_Modules_Default_ApplicationCache_Filter::CONTEXT_CLEARFLAG_REQUEST
                );
            break;

        }

        return $this->_filters[$key];
    }


    public function registerFilter($key, $extractFromExtDirect = false)
    {
        $exp = explode('::', $key);

        $class  = $exp[0];
        $action = $exp[1];

        $thisClass = get_class($this->getActionController());

        if ($thisClass != $class) {

            /**
             * @see Zend_Controller_Action_Exception
             */
            require_once 'Zend/Controller/Action/Exception.php';

            throw new Zend_Controller_Action_Exception(
                "class for filter is not this controller: \"$thisClass\" \"$class\""
            );
        }

        $this->_filterKeys[$thisClass][$action] = $key;
        if ($extractFromExtDirect) {
            $this->_extractFromExtDirect[$thisClass][$action] = true;
        }

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

        $data = $this->getRequest()->getParams();

        if (isset($this->_extractFromExtDirect[$class][$action])) {

            /**
             * @see Conjoon_Keys
             */
            require_once 'Conjoon/Keys.php';

             try {
                $extRequest = Zend_Registry::get(Conjoon_Keys::EXT_REQUEST_OBJECT);
            } catch (Zend_Exception $e) {
                $extRequest = null;
            }

            if ($extRequest && $extRequest->isExtRequest()) {
                $data = $this->getRequest()->getParam('data');
                $data = $data[0];
            }
        }

        $filter->setData($data);

        try {
            $filteredData = $filter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            /**
             * @see Conjoon_Error
             */
            require_once 'Conjoon/Error.php';


            $error = Conjoon_Error::fromFilter($filter, $e);

            /**
             * @see Conjoon_Filter_Exception
             */
            require_once 'Conjoon/Filter/Exception.php';

            throw new Conjoon_Filter_Exception($error->getMessage());
        }

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
