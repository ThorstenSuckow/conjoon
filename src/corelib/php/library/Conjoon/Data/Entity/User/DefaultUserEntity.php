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
 * @see \Conjoon\Data\Entity\User\UserEntity
 */
require_once 'Conjoon/Data/Entity/User/UserEntity.php';

/**
 * @see \Conjoon\User\User
 */
require_once 'Conjoon/User/User.php';


/**
 * Default user entity.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultUserEntity extends UserEntity {

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $firstname
     */
    private $firstname;

    /**
     * @var string $lastname
     */
    private $lastname;

    /**
     * @var string $emailAddress
     */
    private $emailAddress;

    /**
     * @var string $userName
     */
    private $userName;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var boolean $isRoot
     */
    private $isRoot;

    /**
     * @var string $authToken
     */
    private $authToken;

    /**
     * @var integer $lastLogin
     */
    private $lastLogin;


    /**
     * Creates a new instance of this entity.
     *
     */
    public function __construct()
    {
        $this->isRoot = 0;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @inheritdoc
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @inheritdoc
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @inheritdoc
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @inheritdoc
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

   /**
    * @inheritdoc
    */
   public function setIsRoot($isRoot)
    {
        $this->isRoot = $isRoot;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsRoot()
    {
        return $this->isRoot;
    }

    /**
     * @inheritdoc
     */
    public function setAuthToken($authToken)
    {
        $this->authToken = $authToken;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * @inheritdoc
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return
            str_replace(
                array("{id}", "{firstname}", "{lastname}",
                    "{emailAddress}", "{userName}"),
                array($this->getId(), $this->getFirstname(),
                    $this->getLastname(), $this->getEmailAddress(),
                    $this->getUserName()
                ),
                "id:{id};firstname:{firstname};lastname:{lastname};"
                    . "emailAddess:{emailAddress};userName:{userName}]"
            );

    }
}