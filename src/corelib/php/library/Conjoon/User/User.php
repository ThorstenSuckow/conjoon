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

use Conjoon\Lang\Equatable;

/**
 * @see \Conjoon\Lang\Equatable
 */
require_once 'Conjoon/Lang/Equatable.php';

/**
 * An interface representing an user.
 *
 * @category   Conjoon
 * @package    Conjoon_User
 * @subpackage User
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface User extends Equatable {

    /**
     * Returns the id associated with this user.
     *
     * @return string
     */
    public function getId();

    /**
     * Returns the first name of the user.
     *
     * @return string
     */
    public function getFirstname();

    /**
     * Returns the last name of the user.
     *
     * @return string
     */
    public function getLastname();

    /**
     * Returns the email address of the user.
     *
     * @return string
     */
    public function getEmailAddress();

    /**
     * Returns the username of the user.
     *
     * @return string
     */
    public function getUserName();

}
