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
class ImapMessageEntity extends AbstractMessageEntity {

    /**
     *  @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $messageAttachments;

    /**
     * @var string
     */
    protected $messageId;

    public function __construct()
    {
        parent::__construct();

        /**
         * @see \Doctrine\Common\Collections\ArrayCollection
         */
        require_once 'Doctrine/Common/Collections/ArrayCollection.php';

        $this->messageAttachments = new \Doctrine\Common\Collections\ArrayCollection();
    }


    public function addMessageAttachments(
        \Conjoon\Data\Entity\Mail\MessageAttachmentEntity $attachment)
    {
        $this->messageAttachments[] = $attachment;

        return $this;
    }

    public function getMessageAttachments()
    {
        return $this->messageAttachments;
    }

    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getMessageId()
    {
        return $this->messageId;
    }

}