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
    public function setDate($date);

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
     * Add groupwareEmailItemsFlags
     *
     * @param \Conjoon\Data\Entity\Mail\MessageFlagEntity $groupwareEmailItemsFlags
     * @return GroupwareEmailItems
     */
    public function addGroupwareEmailItemsFlag(
        \Conjoon\Data\Entity\Mail\MessageFlagEntity $groupwareEmailItemsFlags);

    /**
     * Remove groupwareEmailItemsFlags
     *
     * @param Conjoon\Data\Entity\GroupwareEmailItemsFlags $groupwareEmailItemsFlags
     */
    public function removeGroupwareEmailItemsFlag(
        \Conjoon\Data\Entity\Mail\MessageFlagEntity $groupwareEmailItemsFlags);

    /**
     * Get groupwareEmailItemsFlags
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getGroupwareEmailItemsFlags();

    /**
     * Set groupwareEmailFolders
     *
     * @param \Conjoon\Data\Entity\Mail\MailFolderEntity $groupwareEmailFolders
     * @return GroupwareEmailItems
     */
    public function setGroupwareEmailFolders(
        \Conjoon\Data\Entity\Mail\MailFolderEntity $groupwareEmailFolders = null);

    /**
     * Get groupwareEmailFolders
     *
     * @return \Conjoon\Data\Entity\Mail\MailFolderEntity
     */
    public function getGroupwareEmailFolders();

}