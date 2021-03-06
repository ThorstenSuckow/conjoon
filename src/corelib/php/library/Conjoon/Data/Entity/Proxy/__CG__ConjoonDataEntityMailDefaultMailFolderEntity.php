<?php

namespace Conjoon\Data\Entity\Proxy\__CG__\Conjoon\Data\Entity\Mail;

/**
 * @see Conjoon\Data\Entity\EntityProxy
 */
require_once 'Conjoon/Data/Entity/EntityProxy.php';

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class DefaultMailFolderEntity extends \Conjoon\Data\Entity\Mail\DefaultMailFolderEntity
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

    public function getName()
    {
        $this->__load();
        return parent::getName();
    }

    public function getIsChildAllowed()
    {
        $this->__load();
        return parent::getIsChildAllowed();
    }

    public function getIsLocked()
    {
        $this->__load();
        return parent::getIsLocked();
    }

    public function getType()
    {
        $this->__load();
        return parent::getType();
    }

    public function getMetaInfo()
    {
        $this->__load();
        return parent::getMetaInfo();
    }

    public function getIsDeleted()
    {
        $this->__load();
        return parent::getIsDeleted();
    }

    public function getParent()
    {
        $this->__load();
        return parent::getParent();
    }

    public function setName($name)
    {
        $this->__load();
        return parent::setName($name);
    }

    public function setIsChildAllowed($isChildAllowed)
    {
        $this->__load();
        return parent::setIsChildAllowed($isChildAllowed);
    }

    public function setIsLocked($isLocked)
    {
        $this->__load();
        return parent::setIsLocked($isLocked);
    }

    public function setType($type)
    {
        $this->__load();
        return parent::setType($type);
    }

    public function setMetaInfo($metaInfo)
    {
        $this->__load();
        return parent::setMetaInfo($metaInfo);
    }

    public function getMailAccounts()
    {
        $this->__load();
        return parent::getMailAccounts();
    }

    public function addMailAccount(\Conjoon\Data\Entity\Mail\MailAccountEntity $mailAccount)
    {
        $this->__load();
        return parent::addMailAccount($mailAccount);
    }

    public function removeMailAccount(\Conjoon\Data\Entity\Mail\MailAccountEntity $mailAccount)
    {
        $this->__load();
        return parent::removeMailAccount($mailAccount);
    }

    public function setIsDeleted($isDeleted)
    {
        $this->__load();
        return parent::setIsDeleted($isDeleted);
    }

    public function setParent(\Conjoon\Data\Entity\Mail\MailFolderEntity $parent = NULL)
    {
        $this->__load();
        return parent::setParent($parent);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'isChildAllowed',
            'isLocked', 'type', 'metaInfo', 'isDeleted', 'parent');
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