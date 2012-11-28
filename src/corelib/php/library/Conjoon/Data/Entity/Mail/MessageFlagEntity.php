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