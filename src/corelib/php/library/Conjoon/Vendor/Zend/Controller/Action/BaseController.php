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

namespace Conjoon\Vendor\Zend\Controller\Action;

/**
 * @see \Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

/**
 * @see \Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * @see \Conjoon_Keys
 */
require_once 'Conjoon/Keys.php';

/**
 * @see \Conjoon_ErrorDto
 */
require_once 'Conjoon/ErrorDto.php';

/**
 * Abstract base class for controllers based on Zend Framework 1.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class BaseController extends \Zend_Controller_Action {

    /**
     * @type \Conjoon\User\AppUser
     */
    protected $appUser;

    /**
     * Creates an error dto based on the specified arguments and returns it.
     *
     * @return \Conjoon_ErrorDto
     */
    protected function getErrorDto($title, $message, $level = 0) {

        $error = new \Conjoon_ErrorDto();
        $error->title = $title;
        $error->message = $message;
        $error->level = $level;
        return $error;

    }

    /**
     * Returns the AppUser currently using this controller.
     *
     * @return \Conjoon\User\AppUser
     */
    protected function getCurrentAppUser() {

        if ($this->appUser) {
            return $this->appUser;
        }

        $auth = \Zend_Registry::get(\Conjoon_Keys::REGISTRY_AUTH_OBJECT);

        /**
         * @see Conjoon_User_AppUser
         */
        require_once 'Conjoon/User/AppUser.php';

        $this->appUser = new \Conjoon\User\AppUser($auth->getIdentity());

        return $this->appUser;
    }

    /**
     * Returns the application configuration
     *
     * @return \stdClass
     */
    protected function getApplicationConfiguration() {

        return \Zend_Registry::get(\Conjoon_Keys::REGISTRY_CONFIG_OBJECT);

    }

}
