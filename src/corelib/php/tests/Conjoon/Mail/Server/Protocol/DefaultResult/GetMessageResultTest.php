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
