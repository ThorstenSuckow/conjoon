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
    protected $_id;

    /**
     * @var string
     */
    protected $_name;

    /**
     * @var bool
     */
    protected $_isChildAllowed;

    /**
     * @var bool
     */
    protected $_isLocked;

    /**
     * @var string
     */
    protected $_type;

    /**
     * @var string
     */
    protected $_metaInfo;

    /**
     * @var bool
     */
    protected $_isDeleted;

    /**
     * @var \Conjoon\Data\Entity\Mail\MailFolderEntity|null
     */
    protected $_parent;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return bool
     */
    public function getIsChildAllowed()
    {
        return $this->_isChildAllowed;
    }

    /**
     * @return bool
     */
    public function getIsLocked()
    {
        return $this->_isLocked;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return string
     */
    public function getMetaInfo()
    {
        return $this->_metaInfo;
    }

    /**
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->_isDeleted;
    }

    /**
     * @return MailFolderEntity|null
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @param $isChildAllowed
     */
    public function setIsChildAllowed($isChildAllowed)
    {
        $this->_isChildAllowed = $isChildAllowed;
    }

    /**
     * @param $isLocked
     */
    public function setIsLocked($isLocked)
    {
        $this->_isLocked = $isLocked;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @param $metaInfo
     */
    public function setMetaInfo($metaInfo)
    {
        $this->_metaInfo = $metaInfo;
    }

    /**
     * @param $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->_isDeleted = $isDeleted;
    }

    /**
     * @param MailFolderEntity $parent
     */
    public function setParent(MailFolderEntity $parent = null)
    {
        $this->_parent = $parent;
    }

}