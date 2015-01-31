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
 * @see \Conjoon\Data\Entity\DataEntity
 */
require_once 'Conjoon/Data/Entity/DataEntity.php';

/**
 * Interface all MessageFlag entities have to implement.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MessageFlagEntity extends \Conjoon\Data\Entity\DataEntity {

    /**
     * Set isRead
     *
     * @param boolean $isRead
     * @return GroupwareEmailItemsFlags
     */
    public function setIsRead($isRead);

    /**
     * Get isRead
     *
     * @return boolean
     */
    public function getIsRead();

    /**
     * Set isSpam
     *
     * @param boolean $isSpam
     * @return GroupwareEmailItemsFlags
     */
    public function setIsSpam($isSpam);

    /**
     * Get isSpam
     *
     * @return boolean
     */
    public function getIsSpam();

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return GroupwareEmailItemsFlags
     */
    public function setIsDeleted($isDeleted);

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted();

    /**
     * Get users
     *
     * @return Conjoon\Data\Entity\User\UserEntity
     */
    public function setUsers(\Conjoon\Data\Entity\User\UserEntity $user);

    /**
     * Get users
     *
     * @return Conjoon\Data\Entity\User\UserEntity
     */
    public function getUsers();

    /**
     * Get users
     *
     * @return Conjoon\Data\Entity\Mail\MessageEntity
     */
    public function setGroupwareEmailItems(\Conjoon\Data\Entity\Mail\MessageEntity $groupwareEmailItems);

    /**
     * Get groupwareEmailItems
     *
     * @return Conjoon\Data\Entity\Mail\MessageEntity
     */
    public function getGroupwareEmailItems();


}