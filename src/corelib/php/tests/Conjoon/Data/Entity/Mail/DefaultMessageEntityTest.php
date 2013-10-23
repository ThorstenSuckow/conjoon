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
 * @see Conjoon\Data\Entity\Mail\DefaultMessageEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultMessageEntity.php';

/**
 * @see Conjoon\Data\Entity\Mail\DefaultMailFolderEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultMailFolderEntity.php';

/**
 * @see Conjoon\Data\Entity\Mail\DefaultAttachmentEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultAttachmentEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMessageEntityTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected function setUp()
    {
        $attachment1 = new \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity;
        $attachment1->setKey('key1');

        $attachment2 = new \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity;
        $attachment2->setKey('key2');

        $this->input = array(
            'groupwareEmailFolders' => new \Conjoon\Data\Entity\Mail\DefaultMailFolderEntity,
            'attachment'            => array($attachment1, $attachment2),
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
            'sender'                => "Sender"
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $message = new DefaultMessageEntity();

        foreach ($this->input as $field => $value) {

            if (is_array($value)) {

                $methodAdd = "add" . ucfirst($field);
                $methodGet = "get" . ucfirst($field) . 's';
                $methodRemove = "remove" . ucfirst($field);
                foreach ($value as $singleEntity) {
                    $message->$methodAdd($singleEntity);
                }

                $results = $message->$methodGet();
                $i = 0;
                foreach ($results as $singleResult) {
                    $this->assertSame($value[$i], $singleResult);
                    $i++;

                    $beforeRemove = $message->$methodGet();
                    $beforeRemoveCount = count($beforeRemove);

                    $message->$methodRemove($singleResult);

                    $afterRemove = $message->$methodGet();
                    $afterRemoveCount = count($afterRemove);

                    $this->assertEquals($beforeRemoveCount - 1, $afterRemoveCount);
                }

                $results = $message->$methodGet();
                $this->assertEquals(0, count($results));

            } else {
                $methodSet = "set" . ucfirst($field);
                $methodGet = "get" . ucfirst($field);
                $message->$methodSet($value);

                $this->assertSame($value, $message->$methodGet());
            }


        }
    }
}
