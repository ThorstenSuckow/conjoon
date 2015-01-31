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
class Conjoon_Controller_Action_Helper_RegistryAccess extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Conjoon_Modules_Default_User $user
     */
    protected $_user = null;

    /**
     * @var string
     */
    protected $_baseUrl = null;

    /**
     * @var string
     */
    protected $_applicationPath = null;

    /**
     * Returns the application path setting.
     *
     * @return string
     */
    public function getApplicationPath()
    {
        if ($this->_applicationPath !== null) {
            return $this->_applicationPath;
        }

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $config = Zend_Registry::get(
            Conjoon_Keys::REGISTRY_CONFIG_OBJECT
        );

        $this->_applicationPath = $config->environment->application_path;

        return $this->_applicationPath;
    }

    /**
     * Returns the base url for this application
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if ($this->_baseUrl !== null) {
            return $this->_baseUrl;
        }

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $config = Zend_Registry::get(
            Conjoon_Keys::REGISTRY_CONFIG_OBJECT
        );

        $this->_baseUrl = $config->environment->base_url;

        return $this->_baseUrl;
    }


    /**
     * Returns the user object as stored in the registry.
     *
     * @return Conjoon_Modules_Default_User
     */
    public function getUser()
    {
        if (!$this->_user) {
            /**
             * @see Conjoon_Keys
             */
            require_once 'Conjoon/Keys.php';

            $user = Zend_Registry::get(
                Conjoon_Keys::REGISTRY_AUTH_OBJECT
            );

            if (!$user) {
                return null;
            }

            $this->_user = $user->getIdentity();
        }

        return $this->_user;
    }

    /**
     * Returns the id of the user stored in the registry.
     *
     * @return integer
     */
    public function getUserId()
    {
        $user = $this->getUser();

        if (!$user) {
            return 0;
        }

        return $user->getId();
    }

    /**
     *
     * @return Conjoon_Controller_Action_Helper_RegistryAccess
     */
    public function direct()
    {
        return $this;
    }

}
