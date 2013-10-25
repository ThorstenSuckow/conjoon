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
