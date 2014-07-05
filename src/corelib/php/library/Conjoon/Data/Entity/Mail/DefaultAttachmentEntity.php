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
 * @see \Conjoon\Data\Entity\Mail\AttachmentEntity
 */
require_once 'Conjoon/Data/Entity/Mail/AttachmentEntity.php';

/**
 * Default message attachment entity.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAttachmentEntity implements AttachmentEntity {

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $encoding;

    /**
     * @var string
     */
    protected $contentId;

    /**
     * @var \Conjoon\Data\Entity\Mail\AttachmentContentEntity
     */
    protected $attachmentContent;

    /**
     * @var \Conjoon\Data\Entity\Mail\MessageEntity
     */
    protected $message;

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
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @inheritdoc
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @inheritdoc
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @inheritdoc
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEncoding()
    {
        return $this->encoding;
    }


    /**
     * @inheritdoc
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @inheritdoc
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAttachmentContent()
    {
        return $this->attachmentContent;
    }

    /**
     * @inheritdoc
     */
    public function setAttachmentContent(
        \Conjoon\Data\Entity\Mail\AttachmentContentEntity $attachmentContent)
    {
        $this->attachmentContent = $attachmentContent;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setMessage(\Conjoon\Data\Entity\Mail\MessageEntity $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return get_class($this) . '@' . spl_object_hash($this);
    }


}
