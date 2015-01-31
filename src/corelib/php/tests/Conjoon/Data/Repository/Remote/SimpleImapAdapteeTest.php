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


namespace Conjoon\Data\Repository\Remote;

/**
 * @see Conjoon\Data\Repository\Remote\SimpleImapAdaptee
 */
require_once dirname(__FILE__) . '/SimpleImapAdaptee.php';

/**
 *
 * @package    Conjoon/Tests
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleImapAdapteeTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConnectionException_Empty()
    {
        $adaptee = new SimpleImapAdaptee();
        $this->assertTrue($adaptee->connect(array()));
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConnectionException_ssl()
    {
        $adaptee = new SimpleImapAdaptee();
        $this->assertTrue($adaptee->connect(array(
            'user'     => 'user',
            'password' => 'password',
            'port'     => 23,
            'host'     => 'imap.host',
            'ssl'      => 'ssl?'
        )));
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConnectionException_missingConfig()
    {
        $adaptee = new SimpleImapAdaptee();
        $this->assertTrue($adaptee->connect(array(
            'user'     => 'user',
            'password' => 'password',
            'port'     => 23,
            'ssl'      => 'ssl?'
        )));
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testSetFlagException_invalidMode()
    {
        $adaptee = new SimpleImapAdaptee();
        $this->assertTrue($adaptee->connect(array(
            'user'     => 'user',
            'password' => 'password',
            'port'     => 23,
            'ssl'      => 'ssl?'
        )));
    }

    /**
     * @expectedException \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function testSetFlag_NoConnection()
    {
        $adaptee = new SimpleImapAdaptee();
        $adaptee->setFlag('\Seen', 1, '+');
    }

    /**
     * @expectedException \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function testSelectFolder_NoConnection()
    {
        $adaptee = new SimpleImapAdaptee();
        $adaptee->selectFolder('INBOX');
    }

    /**
     * @expectedException \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function testGetFolderDelimiter_NoConnection()
    {
        $adaptee = new SimpleImapAdaptee();
        $adaptee->getFolderDelimiter();
    }

    /**
     * @expectedException \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function testGetMessage_NoConnection()
    {
        $adaptee = new SimpleImapAdaptee();
        $adaptee->getMessage(1);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testGetMessage_ExceptionArgument()
    {
        try {
            $adaptee = new SimpleImapAdaptee();

            $adaptee->connect(array(
                'user'     => 'user',
                'password' => 'password',
                'port'     => 23,
                'host'     => 'imap.host',
                'ssl'      => false
            ));
        } catch (\Exception $e) {
            $this->fail();
        }

        $adaptee->getMessage("");
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $adaptee = new SimpleImapAdaptee();

        $this->assertTrue($adaptee->connect(array(
            'user'     => 'user',
            'password' => 'password',
            'port'     => 23,
            'host'     => 'imap.host',
            'ssl'      => false
        )));

        $this->assertEquals(array(
            'header' => "HEADER", 'body' => "BODY"
        ), $adaptee->getMessage(1)
        );

        $this->assertSame('INBOX', $adaptee->selectFolder('INBOX'));
        $this->assertTrue($adaptee->setFlag('\Seen', 1, '-'));
        $this->assertSame('/', $adaptee->getFolderDelimiter());
        $this->assertTrue($adaptee->disconnect());
        $this->assertTrue($adaptee->isConnected());
    }



}