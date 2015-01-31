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
 * @see \Conjoon\Data\Entity\Mail\AbstractMessageEntity
 */
require_once 'Conjoon/Data/Entity/Mail/AbstractMessageEntity.php';

/**
 * Default implementation for Message Entity.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMessageEntity extends AbstractMessageEntity {

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $groupwareEmailItemsFlags;

    /**
     * @var \Conjoon\Data\Entity\Mail\MailFolderEntity
     */
    protected $groupwareEmailFolders;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->groupwareEmailItemsFlags =
            new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function addGroupwareEmailItemsFlag(
        \Conjoon\Data\Entity\Mail\MessageFlagEntity $groupwareEmailItemsFlags)
    {
        $groupwareEmailItemsFlags->setGroupwareEmailItems($this);

        $this->groupwareEmailItemsFlags[] = $groupwareEmailItemsFlags;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeGroupwareEmailItemsFlag(
        \Conjoon\Data\Entity\Mail\MessageFlagEntity $groupwareEmailItemsFlags)
    {
        $this->groupwareEmailItemsFlags->removeElement($groupwareEmailItemsFlags);
    }

    /**
     * @inheritdoc
     */
    public function getGroupwareEmailItemsFlags()
    {
        return $this->groupwareEmailItemsFlags;
    }

    /**
     * @inheritdoc
     */
    public function setGroupwareEmailFolders(
        \Conjoon\Data\Entity\Mail\MailFolderEntity $groupwareEmailFolders = null)
    {
        $this->groupwareEmailFolders = $groupwareEmailFolders;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGroupwareEmailFolders()
    {
        return $this->groupwareEmailFolders;
    }

}
