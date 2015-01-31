<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
 * @see \Conjoon\Data\Entity\Mail\MessageFlagEntity
 */
require_once 'Conjoon/Data/Entity/Mail/MessageFlagEntity.php';

/**
 * Default implementation for MessageFlag
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMessageFlagEntity implements MessageFlagEntity {

    /**
     * @var boolean $isRead
     */
    private $isRead;

    /**
     * @var boolean $isSpam
     */
    private $isSpam;

    /**
     * @var boolean $isDeleted
     */
    private $isDeleted;

    /**
     * @var Conjoon\Data\Entity\User\UserEntity
     */
    private $users;

    /**
     * @var Conjoon\Data\Entity\Mail\MessageEntity
     */
    private $groupwareEmailItems;

    /**
     * Creates a new instance of this entity.
     */
    public function __construct()
    {
        $this->isRead    = 0;
        $this->isSpam    = 0;
        $this->isDeleted = 0;
    }

    /**
     * @inheritdoc
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * @inheritdoc
     */
    public function setIsSpam($isSpam)
    {
        $this->isSpam = $isSpam;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsSpam()
    {
        return $this->isSpam;
    }

    /**
     * @inheritdoc
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @inheritdoc
     */
    public function setUsers(\Conjoon\Data\Entity\User\UserEntity $user)
    {
        $this->users = $user;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @inheritdoc
     */
    public function setGroupwareEmailItems(\Conjoon\Data\Entity\Mail\MessageEntity $groupwareEmailItems)
    {
        $this->groupwareEmailItems = $groupwareEmailItems;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGroupwareEmailItems()
    {
        return $this->groupwareEmailItems;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return get_class($this) . '@' . spl_object_hash($this);
    }

}
