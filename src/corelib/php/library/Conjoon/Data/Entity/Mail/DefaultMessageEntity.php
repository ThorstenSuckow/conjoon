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
 * @see \Conjoon\Data\Entity\Mail\MessageEntity
 */
require_once 'Conjoon/Data/Entity/Mail/MessageEntity.php';

/**
 * Default implementation for Message Entity.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMessageEntity implements MessageEntity {
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var \DateTime $date
     */
    private $date;

    /**
     * @var string $subject
     */
    private $subject;

    /**
     * @var string $from
     */
    private $from;

    /**
     * @var string $replyTo
     */
    private $replyTo;

    /**
     * @var string $to
     */
    private $to;

    /**
     * @var string $cc
     */
    private $cc;

    /**
     * @var string $bcc
     */
    private $bcc;

    /**
     * @var string $inReplyTo
     */
    private $inReplyTo;

    /**
     * @var string $references
     */
    private $references;

    /**
     * @var string $contentTextPlain
     */
    private $contentTextPlain;

    /**
     * @var string $contentTextHtml
     */
    private $contentTextHtml;

    /**
     * @var string $recipients
     */
    private $recipients;

    /**
     * @var string $sender
     */
    private $sender;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $groupwareEmailItemsFlags;

    /**
     * @var \Conjoon\Data\Entity\Mail\MailFolderEntity
     */
    private $groupwareEmailFolders;

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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @inheritdoc
     */
    public function setCc($cc)
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @inheritdoc
     */
    public function setInReplyTo($inReplyTo)
    {
        $this->inReplyTo = $inReplyTo;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getInReplyTo()
    {
        return $this->inReplyTo;
    }

    /**
     * @inheritdoc
     */
    public function setReferences($references)
    {
        $this->references = $references;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @inheritdoc
     */
    public function setContentTextPlain($contentTextPlain)
    {
        $this->contentTextPlain = $contentTextPlain;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContentTextPlain()
    {
        return $this->contentTextPlain;
    }

    /**
     * @inheritdoc
     */
    public function setContentTextHtml($contentTextHtml)
    {
        $this->contentTextHtml = $contentTextHtml;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContentTextHtml()
    {
        return $this->contentTextHtml;
    }

    /**
     * @inheritdoc
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @inheritdoc
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSender()
    {
        return $this->sender;
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