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
