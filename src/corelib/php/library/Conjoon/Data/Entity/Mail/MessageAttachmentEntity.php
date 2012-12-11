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
 * Interface all Message Attachment entities have to implement.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MessageAttachmentEntity extends \Conjoon\Data\Entity\DataEntity {

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set key
     *
     * @param string $key
     * @return MessageAttachmentEntity
     */
    public function setKey($key);

    /**
     * Get key
     *
     * @return string
     */
    public function getKey();

    /**
     * Set filename
     *
     * @param string $fileName
     * @return MessageAttachmentEntity
     */
    public function setFileName($fileName);

    /**
     * Get filename
     *
     * @return string
     */
    public function getFileName();

    /**
     * Get mimetype
     *
     * @return string
     */
    public function getMimeType();

    /**
     * Set mimetype
     *
     * @param string $mimeType
     * @return MessageAttachmentEntity
     */
    public function setMimeType($mimeType);

    /**
     * Get encoding
     *
     * @return string
     */
    public function getEncoding();

    /**
     * Set encoding
     *
     * @param string $encoding
     * @return MessageAttachmentEntity
     */
    public function setEncoding($encoding);

    /**
     * Get contentid
     *
     * @return string
     */
    public function getContentId();

    /**
     * Set contentid
     *
     * @param string $contentId
     * @return MessageAttachmentEntity
     */
    public function setContentId($contentId);

    /**
     * Get content
     *
     * @return null|\Conjoon\Data\Entity\Mail\AttachmentContentEntity
     */
    public function getAttachmentContent();

    /**
     * Set attachmentcontent
     *
     * @param \Conjoon\Data\Entity\Mail\AttachmentContentEntity $attachmentContent
     * @return MessageAttachmentEntity
     */
    public function setAttachmentContent(
        \Conjoon\Data\Entity\Mail\AttachmentContentEntity $attachmentContent);



}