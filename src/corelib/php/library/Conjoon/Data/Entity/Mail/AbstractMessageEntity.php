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
abstract class AbstractMessageEntity implements MessageEntity {
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var \DateTime $date
     */
    protected $date;

    /**
     * @var string $subject
     */
    protected $subject;

    /**
     * @var string $from
     */
    protected $from;

    /**
     * @var string $replyTo
     */
    protected $replyTo;

    /**
     * @var string $to
     */
    protected $to;

    /**
     * @var string $cc
     */
    protected $cc;

    /**
     * @var string $bcc
     */
    protected $bcc;

    /**
     * @var string $inReplyTo
     */
    protected $inReplyTo;

    /**
     * @var string $references
     */
    protected $references;

    /**
     * @var string $contentTextPlain
     */
    protected $contentTextPlain;

    /**
     * @var string $contentTextHtml
     */
    protected $contentTextHtml;

    /**
     * @var string $recipients
     */
    protected $recipients;

    /**
     * @var string $sender
     */
    protected $sender;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $attachments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
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
    public function setDate(\DateTime $date)
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
    public function addAttachment(\Conjoon\Data\Entity\Mail\AttachmentEntity $attachments)
    {
        $attachments->setMessage($this);

        $this->attachments[] = $attachments;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeAttachment(\Conjoon\Data\Entity\Mail\AttachmentEntity $attachments)
    {
        $this->attachments->removeElement($attachments);
    }

    /**
     * @inheritdoc
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return get_class($this) . '@' . spl_object_hash($this);
    }


}
