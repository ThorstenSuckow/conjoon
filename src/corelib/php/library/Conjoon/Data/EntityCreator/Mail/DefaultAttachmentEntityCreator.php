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


namespace Conjoon\Data\EntityCreator\Mail;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see \Conjoon\Data\EntityCreator\Mail\AttachmentEntityCreator
 */
require_once 'Conjoon/Data/EntityCreator/Mail/AttachmentEntityCreator.php';

/**
 * @see \Conjoon\Data\EntityCreator\Mail\MailEntityCreatorException
 */
require_once 'Conjoon/Data/EntityCreator/Mail/MailEntityCreatorException.php';

/**
 *@see \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultAttachmentEntity.php';

/**
 *@see \Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultAttachmentContentEntity.php';

/**
 * Interface all AttachmentEntityCreator classes have to implement.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAttachmentEntityCreator implements AttachmentEntityCreator {

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
                new \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity();
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
