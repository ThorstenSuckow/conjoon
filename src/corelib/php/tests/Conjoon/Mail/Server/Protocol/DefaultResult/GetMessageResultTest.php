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
 * @see SetFlagsResult
 */
require_once 'Conjoon/Mail/Server/Protocol/DefaultResult/GetMessageResult.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class GetMessageResultTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected $date;

    protected function setUp() {
        $this->date = new \DateTime('1970-01-01 00:00:00', new \DateTimeZone('UTC'));
    }

    /**
     * Ensures everathing works as expected
     */
    public function testOk()
    {
        $entity = new \Conjoon\Data\Entity\Mail\ImapMessageEntity();

        $entity->setDate($this->date);
        $entity->setSubject('subject');
        $entity->setTo('to@to.to');
        $entity->setCc('cc@cc.cc');
        $entity->setBcc('bcc@bcc.bcc');
        $entity->setFrom('from@from.from');
        $entity->setReplyTo('replyTo@replyTo.replyTo');
        $entity->setInReplyTo('inReplyTo@inReplyTo.inReplyTo');
        $entity->setReferences('references');
        $entity->setContentTextPlain('contentTextPlain');
        $entity->setContentTextHtml('contentTextHtml');
        $entity->setMessageId('<messageId>');

        $successResult = new GetMessageResult(
            $entity,
            new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                new \Conjoon\Mail\Client\Folder\Folder(
                    new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                        '["1", "2"]'
                    )
                ), "1"
            )
        );

        $this->assertEquals(
            array(
                'message' => array(
                    'id'         => null,
                    'uId'        => "1",
                    'path'       => array('1', '2'),
                    'date'       => $this->date,
                    'subject'    => 'subject',
                    'to'         => 'to@to.to',
                    'cc'         => 'cc@cc.cc',
                    'bcc'        => 'bcc@bcc.bcc',
                    'from'       => 'from@from.from',
                    'replyTo'    => 'replyTo@replyTo.replyTo',
                    'inReplyTo'  => 'inReplyTo@inReplyTo.inReplyTo',
                    'references' => 'references',
                    'messageId'  => '<messageId>',
                    'contentTextHtml'  => 'contentTextHtml',
                    'contentTextPlain' => 'contentTextPlain',
                    'attachments'      => array()
                )
            ),
            $successResult->toArray()
        );

    }

}
