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

        $attEntities = $this->entity->getMessageAttachments();

        for ($i = 0, $len = count($attEntities); $i < $len; $i++) {

            $att =& $attEntities[$i];

            $attachments[] = array(
                'mimeType'  => $att->getMimeType(),
                'encoding'  => $att->getEncoding(),
                'fileName'  => $att->getFileName(),
                'contentId' => $att->getContentId(),
                'key'       => $att->getKey(),
                'content'   => $att->getAttachmentContent()->getContent()
            );

        }

        return array('message' => array(
            'uId' => $this->messageLocation->getUId(),
            'path' => array_merge(
                array($this->messageLocation->getFolder()->getRootId()),
                $this->messageLocation->getFolder()->getPath()
            ),
            'messageId'  => $this->entity->getMessageId(),
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