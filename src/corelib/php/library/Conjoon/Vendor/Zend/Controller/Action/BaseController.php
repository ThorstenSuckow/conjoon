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
 * @see \Conjoon_Error
 */
require_once 'Conjoon/Error.php';

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
