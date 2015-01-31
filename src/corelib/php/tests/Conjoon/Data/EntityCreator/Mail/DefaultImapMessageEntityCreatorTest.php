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
                'messageId'  => '<uniqueid@somegatewy.tld>',
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

                /**
                 * @CN-785
                 */
                if ($key === 'date') {
                    $date = $res->getDate();
                    $this->assertTrue($date instanceof \DateTime);
                    $this->assertSame($date->getTimeZone()->getName(), 'UTC');
                    $this->assertSame($date->format('Y-m-d H:i:s'), '2012-11-19 12:01:38');

                    continue;
                }

                $getter = 'get' . ucfirst($key);

                $this->assertSame(
                    $value,
                    $res->$getter()
                );

            }

        }


    }

}
