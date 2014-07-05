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
 * @see \Conjoon\Data\Entity\DataEntity
 */
require_once 'Conjoon/Data/Entity/DataEntity.php';

/**
 * Interface all Message entities have to implement.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MessageEntity extends \Conjoon\Data\Entity\DataEntity {

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return GroupwareEmailItems
     */
    public function setDate(\DateTime $date);

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate();

    /**
     * Set subject
     *
     * @param string $subject
     * @return GroupwareEmailItems
     */
    public function setSubject($subject);

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject();

    /**
     * Set from
     *
     * @param string $from
     * @return GroupwareEmailItems
     */
    public function setFrom($from);

    /**
     * Get from
     *
     * @return string
     */
    public function getFrom();

    /**
     * Set replyTo
     *
     * @param string $replyTo
     * @return GroupwareEmailItems
     */
    public function setReplyTo($replyTo);

    /**
     * Get replyTo
     *
     * @return string
     */
    public function getReplyTo();

    /**
     * Set to
     *
     * @param string $to
     * @return GroupwareEmailItems
     */
    public function setTo($to);

    /**
     * Get to
     *
     * @return string
     */
    public function getTo();

    /**
     * Set cc
     *
     * @param string $cc
     * @return GroupwareEmailItems
     */
    public function setCc($cc);

    /**
     * Get cc
     *
     * @return string
     */
    public function getCc();

    /**
     * Set bcc
     *
     * @param string $bcc
     * @return GroupwareEmailItems
     */
    public function setBcc($bcc);

    /**
     * Get bcc
     *
     * @return string
     */
    public function getBcc();

    /**
     * Set inReplyTo
     *
     * @param string $inReplyTo
     * @return GroupwareEmailItems
     */
    public function setInReplyTo($inReplyTo);

    /**
     * Get inReplyTo
     *
     * @return string
     */
    public function getInReplyTo();

    /**
     * Set references
     *
     * @param string $references
     * @return GroupwareEmailItems
     */
    public function setReferences($references);

    /**
     * Get references
     *
     * @return string
     */
    public function getReferences();
    /**
     * Set contentTextPlain
     *
     * @param string $contentTextPlain
     * @return GroupwareEmailItems
     */
    public function setContentTextPlain($contentTextPlain);

    /**
     * Get contentTextPlain
     *
     * @return string
     */
    public function getContentTextPlain();

    /**
     * Set contentTextHtml
     *
     * @param string $contentTextHtml
     * @return GroupwareEmailItems
     */
    public function setContentTextHtml($contentTextHtml);

    /**
     * Get contentTextHtml
     *
     * @return string
     */
    public function getContentTextHtml();

    /**
     * Set recipients
     *
     * @param string $recipients
     * @return GroupwareEmailItems
     */
    public function setRecipients($recipients);

    /**
     * Get recipients
     *
     * @return string
     */
    public function getRecipients();

    /**
     * Set sender
     *
     * @param string $sender
     * @return GroupwareEmailItems
     */
    public function setSender($sender);

    /**
     * Get sender
     *
     * @return string
     */
    public function getSender();

    /**
     * Add attachments
     *
     * @param Conjoon\Data\Entity\Mail\AttachmentEntity $attachments
     * @return DefaultMessageEntity
     */
    public function addAttachment(\Conjoon\Data\Entity\Mail\AttachmentEntity $attachments);

    /**
     * Remove attachments
     *
     * @param Conjoon\Data\Entity\Mail\AttachmentEntity $attachments
     */
    public function removeAttachment(\Conjoon\Data\Entity\Mail\AttachmentEntity $attachments);

    /**
     * Get attachments
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getAttachments();

}
