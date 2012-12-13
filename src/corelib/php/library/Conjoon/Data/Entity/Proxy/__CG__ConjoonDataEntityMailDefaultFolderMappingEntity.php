<?php

namespace Conjoon\Data\Entity\Proxy\__CG__\Conjoon\Data\Entity\Mail;

/**
 * @see Conjoon\Data\Entity\EntityProxy
 */
require_once 'Conjoon/Data/Entity/EntityProxy.php';

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class DefaultFolderMappingEntity extends \Conjoon\Data\Entity\Mail\DefaultFolderMappingEntity
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

    public function setGlobalName($globalName)
    {
        $this->__load();
        return parent::setGlobalName($globalName);
    }

    public function getGlobalName()
    {
        $this->__load();
        return parent::getGlobalName();
    }

    public function setType($type)
    {
        $this->__load();
        return parent::setType($type);
    }

    public function getType()
    {
        $this->__load();
        return parent::getType();
    }

    public function setMailAccount(\Conjoon\Data\Entity\Mail\MailAccountEntity $mailAccount = NULL)
    {
        $this->__load();
        return parent::setMailAccount($mailAccount);
    }

    public function getMailAccount()
    {
        $this->__load();
        return parent::getMailAccount();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'globalName', 'type', 'mailAccount');
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