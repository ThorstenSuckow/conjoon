<?php

namespace Conjoon\Data\Entity\Proxy\__CG__\Conjoon\Data\Entity\User;

/**
 * @see Conjoon\Data\Entity\EntityProxy
 */
require_once 'Conjoon/Data/Entity/EntityProxy.php';

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class DefaultUserEntity extends \Conjoon\Data\Entity\User\DefaultUserEntity
    implements \Doctrine\ORM\Proxy\Proxy, \Conjoon\Data\Entity\EntityProxy

{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }


    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function getFirstname()
    {
        $this->__load();
        return parent::getFirstname();
    }

    public function getLastname()
    {
        $this->__load();
        return parent::getLastname();
    }

    public function getEmailAddress()
    {
        $this->__load();
        return parent::getEmailAddress();
    }

    public function getUserName()
    {
        $this->__load();
        return parent::getUserName();
    }

    public function getPassword()
    {
        $this->__load();
        return parent::getPassword();
    }

    public function getIsRoot()
    {
        $this->__load();
        return parent::getIsRoot();
    }

    public function getAuthToken()
    {
        $this->__load();
        return parent::getAuthToken();
    }

    public function getRememberMeToken()
    {
        $this->__load();
        return parent::getRememberMeToken();
    }

    public function getLastLogin()
    {
        $this->__load();
        return parent::getLastLogin();
    }

    public function setFirstname($firstname)
    {
        $this->__load();
        return parent::setFirstname($firstname);
    }

    public function setLastname($lastname)
    {
        $this->__load();
        return parent::setLastname($lastname);
    }

    public function setEmailAddress($emailAddress)
    {
        $this->__load();
        return parent::setEmailAddress($emailAddress);
    }

    public function setUserName($userName)
    {
        $this->__load();
        return parent::setUserName($userName);
    }

    public function setPassword($password)
    {
        $this->__load();
        return parent::setPassword($password);
    }

    public function setIsRoot($isRoot)
    {
        $this->__load();
        return parent::setIsRoot($isRoot);
    }

    public function setAuthToken($authToken)
    {
        $this->__load();
        return parent::setAuthToken($authToken);
    }

    public function setRememberMeToken($rememberMeToken)
    {
        $this->__load();
        return parent::setRememberMeToken($rememberMeToken);
    }

    public function setLastLogin($lastLogin)
    {
        $this->__load();
        return parent::setLastLogin($lastLogin);
    }

    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'firstname', 'lastname',
            'emailAddress', 'userName', 'password', 'isRoot', 'authToken',
            'lastLogin', 'rememberMeToken');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }

    }
}
