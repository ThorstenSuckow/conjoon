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

                    $attachment = new \Conjoon\Data\Entity\Mail\DefaultMessageAttachmentEntity();

                    $attachment->setKey($attData['key']);
                    $attachment->setFileName($attData['fileName']);
                    $attachment->setMimeType($attData['mimeType']);
                    $attachment->setEncoding($attData['encoding']);
                    $attachment->setContentId($attData['contentId']);

                    $mailAttachmentContent = new \Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity();
                    $mailAttachmentContent->setContent($attData['mailAttachmentContent']['content']);

                    $attachment->setAttachmentContent($mailAttachmentContent);

                    $message->addMessageAttachments($attachment);
                }

                $this->assertSame(2, count($message->getMessageAttachments()));

                for ($i = 0, $len = 2; $i < 2; $i++) {
                    $attachment = $message->getMessageAttachments();
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