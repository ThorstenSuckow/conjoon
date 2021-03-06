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
 * A default implematation of an GetMessage request result.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class GetMessageResult implements \Conjoon\Mail\Server\Protocol\SuccessResult {

    /**
     * @var \Conjoon\Data\Entity\Mail\MessageEntity
     */
    protected $entity;

    /**
     * @var \Conjoon\Mail\Client\Message\MessageLocation
     */
    protected $messageLocation;

    /**
     * Creates a new instance of this class.
     *
     * @param \Conjoon\Data\Entity\Mail\MessageEntity $entity
     * @param \Conjoon\Mail\Message\MessageLocation $messageLocation
     */
    public function __construct(
        \Conjoon\Data\Entity\Mail\MessageEntity $entity,
        \Conjoon\Mail\Client\Message\MessageLocation $messageLocation)
    {
        $this->entity = $entity;

        $this->messageLocation = $messageLocation;
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
        $attachments = array();

        $attEntities = $this->entity->getAttachments();

        for ($i = 0, $len = count($attEntities); $i < $len; $i++) {

            $att =& $attEntities[$i];

            $attachments[] = array(
                'mimeType'  => $att->getMimeType(),
                'encoding'  => $att->getEncoding(),
                'fileName'  => $att->getFileName(),
                'contentId' => $att->getContentId(),
                'key'       => $att->getKey()
            );

        }

        return array('message' => array(
            'id'  => $this->entity->getId(),
            'uId' => $this->messageLocation->getUId(),
            'path' => array_merge(
                array($this->messageLocation->getFolder()->getRootId()),
                $this->messageLocation->getFolder()->getPath()
            ),
            /**
             * @todo do we need to have this one in a local message as well?
             */
            'messageId'  => ($this->entity instanceof \Conjoon\Data\Entity\Mail\ImapMessageEntity)
                            ? $this->entity->getMessageId()
                            : null,
            'date'       => $this->entity->getDate(),
            'subject'    => $this->entity->getSubject(),
            'to'         => $this->entity->getTo(),
            'cc'         => $this->entity->getCc(),
            'bcc'        => $this->entity->getBcc(),
            'from'       => $this->entity->getFrom(),
            'replyTo'    => $this->entity->getReplyTo(),
            'inReplyTo'  => $this->entity->getInReplyTo(),
            'references' => $this->entity->getReferences(),
            'contentTextHtml'  => $this->entity->getContentTextHtml(),
            'contentTextPlain' => $this->entity->getContentTextPlain(),
            'attachments'      => $attachments
        ));
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->toJson();
    }

}
