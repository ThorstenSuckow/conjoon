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


namespace Conjoon\Data\Entity\User;

/**
 * @see \Conjoon\Data\Entity\DataEntity
 */
require_once 'Conjoon/Data/Entity/DataEntity.php';

/**
 * @see \Conjoon\User\User
 */
require_once 'Conjoon/User/User.php';

/**
 * Interface all User entities have to implement.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class UserEntity implements \Conjoon\Data\Entity\DataEntity,
    \Conjoon\User\User {

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return Users
     */
    abstract public function setFirstname($firstname);

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return Users
     */
    abstract public function setLastname($lastname);

    /**
     * Set emailAddress
     *
     * @param string $emailAddress
     * @return Users
     */
    abstract public function setEmailAddress($emailAddress);

    /**
     * Set userName
     *
     * @param string $userName
     * @return Users
     */
    abstract public function setUserName($userName);

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    abstract public function setPassword($password);

    /**
     * Get password
     *
     * @return string
     */
    abstract public function getPassword();

    /**
     * Set isRoot
     *
     * @param boolean $isRoot
     * @return Users
     */
    abstract public function setIsRoot($isRoot);

    /**
     * Get isRoot
     *
     * @return boolean
     */
    abstract public function getIsRoot();

    /**
     * Set authToken
     *
     * @param string $authToken
     * @return Users
     */
    abstract public function setAuthToken($authToken);

    /**
     * Set rememberMeToken
     *
     * @param string $rememberMeToken
     * @return Users
     */
    abstract public function setRememberMeToken($rememberMeToken);

    /**
     * Get authToken
     *
     * @return string
     */
    abstract public function getAuthToken();

    /**
     * Get rememberMeToken
     *
     * @return string
     */
    abstract public function getRememberMeToken();

    /**
     * Set lastLogin
     *
     * @param integer $lastLogin
     * @return Users
     */
    abstract public function setLastLogin($lastLogin);

    /**
     * Get lastLogin
     *
     * @return integer
     */
    abstract public function getLastLogin();

}
