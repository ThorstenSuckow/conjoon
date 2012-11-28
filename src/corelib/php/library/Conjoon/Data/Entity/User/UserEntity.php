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
 * Interface all User entities have to implement.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface UserEntity extends \Conjoon\Data\Entity\DataEntity {

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return Users
     */
    public function setFirstname($firstname);

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname();
    /**
     * Set lastname
     *
     * @param string $lastname
     * @return Users
     */
    public function setLastname($lastname);

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname();

    /**
     * Set emailAddress
     *
     * @param string $emailAddress
     * @return Users
     */
    public function setEmailAddress($emailAddress);

    /**
     * Get emailAddress
     *
     * @return string
     */
    public function getEmailAddress();

    /**
     * Set userName
     *
     * @param string $userName
     * @return Users
     */
    public function setUserName($userName);

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName();

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    public function setPassword($password);

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword();

    /**
     * Set isRoot
     *
     * @param boolean $isRoot
     * @return Users
     */
    public function setIsRoot($isRoot);

    /**
     * Get isRoot
     *
     * @return boolean
     */
    public function getIsRoot();

    /**
     * Set authToken
     *
     * @param string $authToken
     * @return Users
     */
    public function setAuthToken($authToken);

    /**
     * Get authToken
     *
     * @return string
     */
    public function getAuthToken();

    /**
     * Set lastLogin
     *
     * @param integer $lastLogin
     * @return Users
     */
    public function setLastLogin($lastLogin);

    /**
     * Get lastLogin
     *
     * @return integer
     */
    public function getLastLogin();

}