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

namespace Conjoon\User;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see Conjoon_User_User
 */
require_once 'Conjoon/User/DefaultUser.php';

/**
 * An implementation of Conjoon_User_User.
 *
 * @category   Conjoon
 * @package    Conjoon_User
 * @subpackage User
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

class AppUser extends DefaultUser {

    /**
     * @inheritdoc
     */
    public function __construct($options)
    {
        /**
         * @see \Conjoon\Argument\Check
         */
        require_once 'Conjoon/Argument/ArgumentCheck.php';

        $data = array('user' => $options);

        ArgumentCheck::check(array(
            'user' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon_Modules_Default_User'
            )
        ), $data);

        $options = $data['user'];

        if ($options->getId() == ""
            || $options->getFirstName()  == ""
            || $options->getLastName() == ""
            || $options->getUsername() == ""
            || $options->getEmailAddress() == "") {
            /**
             * @see Conjoon\User\UserException
             */
            require_once 'Conjoon/User/UserException.php';

            throw new UserException(
                "Cannot use instance of Conjoon_Modules_Default_User - "
                . "object data is not valid"
            );
        }

        $this->_id           = (string) $options->getId();
        $this->_firstName    = (string) $options->getFirstName();
        $this->_lastName     = (string) $options->getLastName();
        $this->_userName     = (string) $options->getUsername();
        $this->_emailAddress = (string) $options->getEmailAddress();
    }
}