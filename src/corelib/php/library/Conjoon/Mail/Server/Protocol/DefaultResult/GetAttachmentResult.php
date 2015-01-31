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
