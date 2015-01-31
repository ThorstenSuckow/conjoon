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
 * @see \Conjoon\Data\Entity\DataEntity
 */
require_once 'Conjoon/Data/Entity/DataEntity.php';

/**
 * Interface all Attachment entities have to implement.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface AttachmentEntity extends \Conjoon\Data\Entity\DataEntity {

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
     * @return AttachmentEntity
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
     * @return AttachmentEntity
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
     * @return AttachmentEntity
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
     * @return AttachmentEntity
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
     * @return AttachmentEntity
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
     * @return AttachmentEntity
     */
    public function setAttachmentContent(
        \Conjoon\Data\Entity\Mail\AttachmentContentEntity $attachmentContent);

    /**
     * Set message
     *
     * @param Conjoon\Data\Entity\Mail\MessageEntity $message
     * @return DefaultAttachmentEntity
     */
    public function setMessage(\Conjoon\Data\Entity\Mail\MessageEntity $message);

    /**
     * Get message
     *
     * @return Conjoon\Data\Entity\Mail\MessageEntity
     */
    public function getMessage();

}
