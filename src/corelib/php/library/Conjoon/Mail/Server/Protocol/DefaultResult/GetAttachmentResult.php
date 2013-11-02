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


namespace Conjoon\Mail\Server\Protocol\DefaultResult;

/**
 * @see \Conjoon\Mail\Server\Protocol\SuccessResult
 */
require_once 'Conjoon/Mail/Server/Protocol/SuccessResult.php';

/**
 * A default implematation of an GetAttachment request result.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class GetAttachmentResult implements \Conjoon\Mail\Server\Protocol\SuccessResult {

    /**
     * @var \Conjoon\Data\Entity\Mail\AttachmentEntity
     */
    protected $entity;

    /**
     * @var \Conjoon\Mail\Client\Message\AttachmentLocation
     */
    protected $attachmentLocation;

    /**
     * Creates a new instance of this class.
     *
     * @param \Conjoon\Data\Entity\Mail\AttachmentEntity $entity
     * @param \Conjoon\Mail\Message\AttachmentLocation $attachmentLocation
     */
    public function __construct(
        \Conjoon\Data\Entity\Mail\AttachmentEntity $entity,
        \Conjoon\Mail\Client\Message\AttachmentLocation $attachmentLocation)
    {
        $this->entity = $entity;

        $this->attachmentLocation = $attachmentLocation;
    }

    /**
     * @inheritdoc
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return array(
            'mimeType'  => $this->entity->getMimeType(),
            'encoding'  => $this->entity->getEncoding(),
            'fileName'  => $this->entity->getFileName(),
            'contentId' => $this->entity->getContentId(),
            'key'       => $this->entity->getKey(),
            'content'   => $this->entity->getAttachmentContent()
                           ? $this->entity->getAttachmentContent()->getContent()
                           : ""
        );
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->toJson();
    }

}
