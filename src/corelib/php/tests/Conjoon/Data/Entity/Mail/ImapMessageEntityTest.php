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

namespace Conjoon\Data\Entity\Mail;

/**
 * @see Conjoon\Data\Entity\Mail\ImapMessageEntity
 */
require_once 'Conjoon/Data/Entity/Mail/ImapMessageEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ImapMessageEntityTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected function setUp()
    {
        $this->input = array(
            'date'                  => new \DateTime,
            'subject'               => "subject",
            'from'                  => "from",
            'replyTo'               => "replyTo",
            'to'                    => "To",
            'cc'                    => "cc",
            'bcc'                   => "bcc",
            'inReplyTo'             => "in_reply_to",
            'references'            => "References",
            'messageId'             => '<messageId>',
            'contentTextPlain'      => "content text plain",
            'contentTextHtml'       => "content text html",
            'recipients'            => "Recipients",
            'sender'                => "Sender",
            'attachments'           => array(
                array(
                    'key' => 'key',
                    'fileName' => 'filename',
                    'mimeType' => 'mimetype',
                    'encoding' => 'encoding',
                    'mailAttachmentContent' => array(
                        'content' => 'CONTENTYO'
                    ),
                    'contentId' => 'contentId'
                ),
                array(
                    'key' => 'key1',
                    'fileName' => 'filename1',
                    'mimeType' => 'mimetype1',
                    'encoding' => 'encoding1',
                    'mailAttachmentContent' => array(
                        'content' => 'CONTENTYO1'
                    ),
                    'contentId' => 'contentId1'
                )
            )
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $message = new ImapMessageEntity();

        foreach ($this->input as $field => $value) {

            if ($field == 'attachments') {
                for ($i = 0, $len = count($value); $i < $len; $i++) {
                    $attData =& $value[$i];

                    $attachment = new \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity();

                    $attachment->setKey($attData['key']);
                    $attachment->setFileName($attData['fileName']);
                    $attachment->setMimeType($attData['mimeType']);
                    $attachment->setEncoding($attData['encoding']);
                    $attachment->setContentId($attData['contentId']);

                    $mailAttachmentContent = new \Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity();
                    $mailAttachmentContent->setContent($attData['mailAttachmentContent']['content']);

                    $attachment->setAttachmentContent($mailAttachmentContent);

                    $message->addAttachment($attachment);
                }

                $this->assertSame(2, count($message->getAttachments()));

                for ($i = 0, $len = 2; $i < 2; $i++) {
                    $attachment = $message->getAttachments();
                    $attachment = $attachment[$i];

                    $attData =& $value[$i];

                    $this->assertSame($attData['key'], $attachment->getKey());
                    $this->assertSame($attData['fileName'], $attachment->getFileName());
                    $this->assertSame($attData['mimeType'], $attachment->getMimeType());
                    $this->assertSame($attData['encoding'], $attachment->getEncoding());
                    $this->assertSame($attData['contentId'], $attachment->getContentId());

                    $mailAttachmentContent = $attachment->getAttachmentContent();

                    $this->assertSame(
                        $attData['mailAttachmentContent']['content'],
                        $mailAttachmentContent->getContent()
                    );

                }

                continue;
            }

            $methodSet = "set" . ucfirst($field);
            $methodGet = "get" . ucfirst($field);
            $message->$methodSet($value);

            $this->assertSame($value, $message->$methodGet());
        }
    }
}
