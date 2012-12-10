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
     * inheritdoc
     */
    public function createFrom(\Conjoon\Mail\Message\RawMessage $message)
    {
        $header = $this->parseHeader($message->getHeader());

        $body = $this->parseBody(
            $message->getHeader() . "\n\n" . $message->getBody());

        $message = new \Conjoon\Data\Entity\Mail\ImapMessageEntity();

        $message->setDate($header['date']);
        $message->setSubject($header['subject']);
        $message->setTo($header['to']);
        $message->setCc($header['cc']);
        $message->setBcc($header['bcc']);
        $message->setFrom($header['from']);
        $message->setReplyTo($header['replyTo']);
        $message->setInReplyTo($header['inReplyTo']);
        $message->setReferences($header['references']);

        $message->setContentTextPlain($body['contentTextPlain']);
        $message->setContentTextHtml($body['contentTextHtml']);


        return $message;
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

            $header['date'] = \Conjoon_Date_Format::toUtc(
                $sanitizeDateTransformer->transform($header['date'])
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