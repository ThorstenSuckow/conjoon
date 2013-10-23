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

/**
 * @see Conjoon\Data\EntityCreator\Mail\DefaultAttachmentEntityCreator
 */
require_once 'Conjoon/Data/EntityCreator/Mail/DefaultAttachmentEntityCreator.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAttachmentEntityCreatorTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected $creator;

    protected function setUp()
    {
        parent::setUp();

        $this->input = array(
            "From: toaddress@domain.tld\n"
                . "Reply-To: replyname@domain.tld\n"
                . "To: Thorsten Suckow-Homberg <demo-registration@conjoon.org>\n"
                . "Subject:  . . . reg for [someuser@domainname.tld]\n"
                . "Date: Mon, 19 Nov 2012 13:01:38 +0100\n"
                . "Content-Type: text/plain; charset=iso-8859-1\n"
                . "Content-Transfer-Encoding: quoted-printable\n"
                . "Content-Disposition: inline\n"
                . "MIME-Version: 1.0\n"
                . "Message-Id: <uniqueid@somegatewy.tld>"
            => array(
                'from'       => 'toaddress@domain.tld',
                'replyTo'    => 'replyname@domain.tld',
                'to'         => 'Thorsten Suckow-Homberg <demo-registration@conjoon.org>',
                'subject'    => '. . . reg for [someuser@domainname.tld]',
                'date'       => '2012-11-19 12:01:38',
                'cc'         => '',
                'references' => '',
                'inReplyTo'  => ''
            )
        );

        $this->creator = new DefaultAttachmentEntityCreator();
    }

    public function testCreateListFrom()
    {
        foreach ($this->input as $input => $result) {
            $rawMessage = new \Conjoon\Mail\Message\DefaultRawMessage($input, "");
            $res = $this->creator->createListFrom($rawMessage);

            $this->assertTrue(is_array($res));
            $this->assertTrue(empty($res));
        }
    }

    /**
     * @expectedException \Conjoon\Data\EntityCreator\Mail\MailEntityCreatorException
     */
    public function testCreateFrom_WithException()
    {
        $this->creator->createFrom(array('test'));
    }

    public function testOk()
    {
        $entity = $this->creator->createFrom(array(
            'content'   => 'toMd5Please',
            'contentId' => '',
            'fileName'  => 'sfsf',
            'mimeType'  => 'sfsf',
            'encoding'  => '',
        ));

        $this->assertTrue($entity instanceof \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity);

        $this->assertSame(md5('toMd5Please'), $entity->getKey());

        $this->assertSame('toMd5Please', $entity->getAttachmentContent()->getContent());

    }
}
