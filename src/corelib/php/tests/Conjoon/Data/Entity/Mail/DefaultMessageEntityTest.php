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
