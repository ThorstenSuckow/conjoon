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