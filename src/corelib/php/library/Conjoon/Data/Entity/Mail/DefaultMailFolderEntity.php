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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $mailAccounts;

    /**
     * Creates a new instance of this class.
     *
     */
    public function __construct()
    {
        /**
         * @see \Doctrine\Common\Collections\ArrayCollection
         */
        require_once 'Doctrine/Common/Collections/ArrayCollection.php';

        $this->mailAccounts = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    /**
     * Get Accounts
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMailAccounts()
    {
        return $this->mailAccounts;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return get_class($this) . '@' . spl_object_hash($this);
    }

}
