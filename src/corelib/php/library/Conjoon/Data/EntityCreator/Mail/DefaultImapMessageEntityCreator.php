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

use Conjoon\Data\EntityCreator\Mail\MailEntityCreatorException;

/**
 * @see \Conjoon\Data\EntityCreator\Mail\MailEntityCreatorException
 */
require_once 'Conjoon/Data/EntityCreator/Mail/MailEntityCreatorException.php';

/**
 *@see \Conjoon\Data\Entity\Mail\ImapMessageEntity
 */
require_once 'Conjoon/Data/Entity/Mail/ImapMessageEntity.php';

/**
 * @see Conjoon\Data\EntityCreator\Mail\ImapMessageEntityCreator
 */
require_once 'Conjoon/Data/EntityCreator/Mail/ImapMessageEntityCreator.php';

/**
 * Default implementation for an ImapMessageEntityCreator.
 *
 * @uses ImapMessageEntityCreator
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultImapMessageEntityCreator implements ImapMessageEntityCreator {

    /**
     * @var \Conjoon\Data\EntityCreator\Mail\AttachmentEntityCreator
     */
    protected $attachmentCreator;

    /**
     * inheritdoc
     */
    public function createFrom(\Conjoon\Mail\Message\RawMessage $message)
    {
        $header = $this->parseHeader($message->getHeader());

        $body = $this->parseBody(
            $message->getHeader() . "\n\n" . $message->getBody());

        $message = new \Conjoon\Data\Entity\Mail\ImapMessageEntity();

        $message->setMessageId($header['messageId']);
        $message->setDate($header['date']);
        $message->setSubject($header['subject']);
        $message->setTo($header['to']);
        $message->setCc($header['cc']);
        $message->setBcc($header['bcc']);
        $message->setFrom($header['from']);
        $message->setReplyTo($header['replyTo']);
        $message->setInReplyTo($header['inReplyTo']);
        $message->setReferences($header['references']);

        $this->createAttachments($message, $body);

        $message->setContentTextPlain($body['contentTextPlain']);
        $message->setContentTextHtml($body['contentTextHtml']);


        return $message;
    }

    /**
     * Adds attachments to the message if atatchments are available.
     *
     * @param \Conjoon\Data\Entity\Mail\ImapMessageEntity $message
     * @param array $parsedMessage
     */
    protected function createAttachments(
            \Conjoon\Data\Entity\Mail\ImapMessageEntity $message,
            array $parsedMessage)
    {
        if (!isset($parsedMessage['attachments'])
            || !is_array($parsedMessage['attachments'])) {
            return;
        }

        if (!$this->attachmentCreator) {
            /**
             * @see \Conjoon\Data\EntityCreator\Mail\DefaultAttachmentEntityCreator
             */
            require_once 'Conjoon/Data/EntityCreator/Mail/DefaultAttachmentEntityCreator.php';

            $this->attachmentCreator =
                new \Conjoon\Data\EntityCreator\Mail\DefaultAttachmentEntityCreator();
        }

        for ($i = 0, $len = count($parsedMessage['attachments']); $i < $len; $i++) {
            $attachment =& $parsedMessage['attachments'][$i];

            $attachmentEntity = $this->attachmentCreator->createFrom($attachment);

            $message->addAttachment($attachmentEntity);
        }

    }

    /**
     * Parses the body into an array.
     *
     * @param string $messageText The raw message text
     *
     * @return array
     */
    protected function parseBody($messageText)
    {
        try {
            /**
             * @see \Conjoon\Text\Parser\Mail\MessageContentParser
             */
            require_once 'Conjoon/Text/Parser/Mail/MessageContentParser.php';

            $parser = new \Conjoon\Text\Parser\Mail\MessageContentParser();

            $body = $parser->parse($messageText);

        } catch (\Exception $e) {

            throw new MailEntityCreatorException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

        return $body;
    }

    /**
     * Parses the header into an array.
     *
     * @param string $header The raw header
     *
     * @return array
     *
     * @throws MailEntityCreatorException
     */
    protected function parseHeader($header)
    {
        try {

            /**
             * @see \Conjoon_Text_Parser_Mail_MessageHeaderParser
             */
            require_once 'Conjoon/Text/Parser/Mail/MessageHeaderParser.php';

            $parser = new \Conjoon_Text_Parser_Mail_MessageHeaderParser();

            $header = $parser->parse($header);

            /**
             * @see \Conjoon_Text_Transformer_DateStringSanitizer
             */
            require_once 'Conjoon/Text/Transformer/DateStringSanitizer.php';

            $sanitizeDateTransformer = new \Conjoon_Text_Transformer_DateStringSanitizer();

            /**
             * @see \Conjoon_Date_Format
             */
            require_once 'Conjoon/Date/Format.php';

            $header['date'] = new \DateTime(
                \Conjoon_Date_Format::toUtc(
                    $sanitizeDateTransformer->transform($header['date'])
                ), new \DateTimeZone('UTC')
            );

            /**
             * @see Conjoon_Text_Transformer_MimeDecoder
             */
            require_once 'Conjoon/Text/Transformer/MimeDecoder.php';

            $mimeDecoder = new \Conjoon_Text_Transformer_MimeDecoder();

            $header['subject'] = $mimeDecoder->transform($header['subject']);

            $header['to']      = isset($header['to']) ? $header['to'] : "";
            $header['cc']      = isset($header['cc']) ? $header['cc'] : "";
            $header['bcc']     = isset($header['bcc']) ? $header['bcc'] : "";
            $header['from']    = isset($header['from']) ? $header['from'] : "";
            $header['replyTo'] = isset($header['replyTo']) ? $header['replyTo'] : "";

            $header['inReplyTo'] = isset($header['inReplyTo'])
                                   ? $header['inReplyTo'] : "";

            $header['references'] = isset($header['references'])
                                    ? $header['references'] : "";

            return $header;

        } catch (\Exception $e) {

            throw new MailEntityCreatorException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }
    }

}
