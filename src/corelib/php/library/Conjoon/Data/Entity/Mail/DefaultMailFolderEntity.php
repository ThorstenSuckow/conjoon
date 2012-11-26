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

namespace Conjoon\Data\Entity\Mail;

/**
 * @see \Conjoon\Data\Entity\Mail\MailFolderEntity
 */
require_once 'Conjoon/Data/Entity/Mail/MailFolderEntity.php';

/**
 * Default implementation for Conjoon_Data_Entity_Mail_MailFolderEntity
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailFolderEntity implements MailFolderEntity {

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $isChildAllowed;

    /**
     * @var bool
     */
    protected $isLocked;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $metaInfo;

    /**
     * @var bool
     */
    protected $isDeleted;

    /**
     * @var \Conjoon\Data\Entity\Mail\MailFolderEntity|null
     */
    protected $parent;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function getIsChildAllowed()
    {
        return $this->isChildAllowed;
    }

    /**
     * @return bool
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getMetaInfo()
    {
        return $this->metaInfo;
    }

    /**
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @return MailFolderEntity|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param $isChildAllowed
     */
    public function setIsChildAllowed($isChildAllowed)
    {
        $this->isChildAllowed = $isChildAllowed;

        return $this;
    }

    /**
     * @param $isLocked
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param $metaInfo
     */
    public function setMetaInfo($metaInfo)
    {
        $this->metaInfo = $metaInfo;

        return $this;
    }

    /**
     * @param $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @param MailFolderEntity $parent
     */
    public function setParent(MailFolderEntity $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

}