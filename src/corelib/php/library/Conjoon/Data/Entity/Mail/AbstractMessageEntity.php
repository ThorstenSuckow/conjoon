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

}