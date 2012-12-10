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
 * @see Conjoon\Data\EntityCreator\Mail\DefaultImapMessageEntityCreator
 */
require_once 'Conjoon/Data/EntityCreator/Mail/DefaultImapMessageEntityCreator.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultImapMessageEntityCreatorTest extends \PHPUnit_Framework_TestCase {

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

        $this->creator = new DefaultImapMessageEntityCreator();
    }

    public function testOk()
    {
        foreach ($this->input as $input => $result) {
            $rawMessage = new \Conjoon\Mail\Message\DefaultRawMessage($input, "");

            $res = $this->creator->createFrom($rawMessage);

            foreach ($result as $key => $value) {

                $getter = 'get' . ucfirst($key);

                $this->assertSame(
                    $value,
                    $res->$getter()
                );

            }

        }


    }

}
