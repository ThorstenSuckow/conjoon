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


namespace Conjoon\Data\EntityCreator\Mail;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see \Conjoon\Data\EntityCreator\Mail\MessageAttachmentEntityCreator
 */
require_once 'Conjoon/Data/EntityCreator/Mail/MessageAttachmentEntityCreator.php';

/**
 * @see \Conjoon\Data\EntityCreator\Mail\MailEntityCreatorException
 */
require_once 'Conjoon/Data/EntityCreator/Mail/MailEntityCreatorException.php';

/**
 *@see \Conjoon\Data\Entity\Mail\DefaultMessageAttachmentEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultMessageAttachmentEntity.php';

/**
 *@see \Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultAttachmentContentEntity.php';

/**
 * Interface all MessageAttachmentEntityCreator classes have to implement.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMessageAttachmentEntityCreator implements MessageAttachmentEntityCreator {

    /**
     * @inheritdoc
     */
    public function createListFrom(\Conjoon\Mail\Message\RawMessage $message)
    {
        $attachments = array();

        try {
            /**
             * @see \Conjoon\Text\Parser\Mail\MessageContentParser
             */
            require_once 'Conjoon/Text/Parser/Mail/MessageContentParser.php';

            $parser = new \Conjoon\Text\Parser\Mail\MessageContentParser();

            $body = $parser->parse(
                $message->getHeader() . "\n\n" . $message->getBody()
            );

            $att =& $body['attachments'];

            for ($i = 0, $len = count($att); $i < $len; $i++) {
                $attachments[] = $this->createFrom($att[$i]);
            }

            return $attachments;
        } catch (\Exception $e) {

            throw new MailEntityCreatorException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function createFrom(array $options)
    {
        $stringEmptyCheck = array(
            'type'       => 'string',
            'allowEmpty' => true
        );
        $stringCheck = array(
            'type'       => 'string',
            'allowEmpty' => false
        );

        try {

            ArgumentCheck::check(array(
                'mimeType'  => $stringEmptyCheck,
                'encoding'  => $stringEmptyCheck,
                'fileName'  => $stringCheck,
                'content'   => $stringEmptyCheck,
                'contentId' => $stringEmptyCheck
            ), $options);

            $attachmentEntity  =
                new \Conjoon\Data\Entity\Mail\DefaultMessageAttachmentEntity();
            $attachmentContent =
                new \Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity();

            $attachmentEntity->setMimeType($options['mimeType']);
            $attachmentEntity->setEncoding($options['encoding']);
            $attachmentEntity->setFileName($options['fileName']);
            $attachmentEntity->setContentId($options['contentId']);
            $attachmentEntity->setKey(md5($options['content']));

            $attachmentContent->setContent($options['content']);

            $attachmentEntity->setAttachmentContent($attachmentContent);

            return $attachmentEntity;

        } catch (\Exception $e) {
            throw new MailEntityCreatorException(
                "Excpetion thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

    }

}