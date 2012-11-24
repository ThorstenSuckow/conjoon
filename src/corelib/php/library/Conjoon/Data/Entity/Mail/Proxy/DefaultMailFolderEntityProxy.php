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

namespace Conjoon\Data\Entity\Mail\Proxy;

use \Conjoon\Argument\InvalidArgumentException;

use \Conjoon\Argument\ArgumentCheck;

/**
 * @see \Conjoon\Data\Entity\Mail\DefaultMailFolderEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultMailFolderEntity.php';

/**
 * @see \Conjoon\Data\Entity\EntityProxy
 */
require_once 'Conjoon/Data/Entity/EntityProxy.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * Default implementation for \Conjoon\Data\Entity\Mail\DefaultMailFolderEntityProxy
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailFolderEntityProxy
    extends \Conjoon\Data\Entity\Mail\DefaultMailFolderEntity
    implements \Conjoon\Data\Entity\EntityProxy {

    /**
     * @var bool
     */
    protected $__isLoaded__ = false;

    /**
     * @var \Conjoon\Data\Repository\Mail\MailFolderRepository
     */
    protected $__repository__;

    /**
     * @var mixed
     */
    protected $__identifier__;

    /**
     * @param \Conjoon\Data\Mail\MailFolderRepository $repository
     * @param mixed $id
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(
        \Conjoon\Data\Repository\DataRepository $repository, $id)
    {
        $data = array('repository' => $repository, 'id' => $id);

        ArgumentCheck::check(array(
            'repository' => array(
                'type'       => 'instanceof',
                'class'      => '\Conjoon\Data\Repository\Mail\MailFolderRepository',
                'allowEmpty' => false
            ),
            'id' => array(
                'type'        => 'int',
                'allowEmpty'  => false,
                'greaterThan' => 0
            )
        ), $data);

        $id = $data['id'];

        $this->__repository__ = $repository;
        $this->__identifier__ = $id;
    }

    /**
     *
     */
    protected function __load()
    {
        if ($this->__isLoaded__) {
            return;
        }

        $this->__isLoaded__ = true;

        $entity = $this->__repository__->findById($this->__identifier__);

        if ($entity === null) {

            /**
             * @see \Conjoon\Data\Entity\EntityNotFoundException
             */
            require_once '\Conjoon\Data\Entity\EntityNotFoundException.php';

            throw new \Conjoon\Data\Entity\EntityNotFoundException(
                "entity not found for id " . $this->__identifier__
            );
        }

        $this->_id             = $entity->getId();
        $this->_name           = $entity->getName();
        $this->_isChildAllowed = $entity->getIsChildAllowed();
        $this->_isLocked       = $entity->getIsLocked();
        $this->_type           = $entity->getType();
        $this->_metaInfo       = $entity->getMetaInfo();
        $this->_isDeleted      = $entity->getIsDeleted();
        $this->_parent         = $entity->getParent();

        unset($this->__repository__, $this->__identifier__);

    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        $this->__load();
        return parent::getId();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        $this->__load();
        return parent::getName();
    }

    /**
     * @inheritdoc
     */
    public function getIsChildAllowed()
    {
        $this->__load();
        return parent::getIsChildAllowed();
    }

    /**
     * @inheritdoc
     */
    public function getIsLocked()
    {
        $this->__load();
        return parent::getIsLocked();
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        $this->__load();
        return parent::getType();
    }

    /**
     * @inheritdoc
     */
    public function getMetaInfo()
    {
        $this->__load();
        return parent::getMetaInfo();
    }

    /**
     * @inheritdoc
     */
    public function getIsDeleted()
    {
        $this->__load();
        return parent::getIsDeleted();
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        $this->__load();
        return parent::getParent();
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->__load();
        return parent::setId($id);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->__load();
        return parent::setName($name);
    }

    /**
     * @inheritdoc
     */
    public function setIsChildAllowed($isChildAllowed)
    {
        $this->__load();
        return parent::setIsChildAllowed($isChildAllowed);
    }

    /**
     * @inheritdoc
     */
    public function setIsLocked($isLocked)
    {
        $this->__load();
        return parent::setIsLocked($isLocked);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->__load();
        return parent::setType($type);
    }

    /**
     * @inheritdoc
     */
    public function setMetaInfo($metaInfo)
    {
        $this->__load();
        return parent::setMetaInfo($metaInfo);
    }

    /**
     * @inheritdoc
     */
    public function setIsDeleted($isDeleted)
    {
        $this->__load();
        return parent::setIsDeleted($isDeleted);
    }

    /**
     * @inheritdoc
     */
    public function setParent(\Conjoon\Data\Entity\Mail\MailFolderEntity $parent = null)
    {
        $this->__load();
        return parent::setParent($parent);
    }

}