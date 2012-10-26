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
