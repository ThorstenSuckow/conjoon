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
 * @see Conjoon\Data\Repository\Remote\DefaultImapConnection
 */
require_once 'Conjoon/Data/Repository/Remote/DefaultImapConnection.php';

/**
 * @package Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultImapConnectionTest extends \PHPUnit_Framework_TestCase {


    protected $config;

    protected function setUp()
    {
        $this->config = array(
            'imapAdaptee' => new SimpleImapAdaptee()
        );

        $this->flagCollection = new \Conjoon\Mail\Client\Message\Flag\DefaultFlagCollection(
            '[{"id":"173","isRead":false},{"id":"172","isRead":true}]'
        );

        $this->folderPath = new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
            '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
        );

    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConnectionException_Empty()
    {
        $connection = new DefaultImapConnection($this->config);
        $this->assertTrue($connection->connect(array()));
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConnectionException_ssl()
    {
        $connection = new DefaultImapConnection($this->config);
        $this->assertTrue($connection->connect(array(
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
        $connection = new DefaultImapConnection($this->config);
        $this->assertTrue($connection->connect(array(
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
        $connection = new DefaultImapConnection($this->config);
        $this->assertTrue($connection->connect(array(
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
        $connection = new DefaultImapConnection($this->config);
        $connection->setFlags($this->flagCollection);
    }

    /**
     * @expectedException \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function testSelectFolder_NoConnection()
    {
        $connection = new DefaultImapConnection($this->config);
        $connection->selectFolder($this->folderPath);
    }

    /**
     * @expectedException \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function testGetFolderDelimiter_NoConnection()
    {
        $connection = new DefaultImapConnection($this->config);
        $connection->getFolderDelimiter();
    }

    /**
     * @expectedException \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function testGetMessage_NoConnection()
    {
        $connection = new DefaultImapConnection($this->config);
        $connection->getMessage("1");
    }

    /**
     * @expectedException \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    public function testGetMessage_ArgumentException()
    {
        try {
            $connection = new DefaultImapConnection($this->config);
            $connection->connect(array(
                'user'     => 'user',
                'password' => 'password',
                'port'     => 23,
                'host'     => 'imap.host',
                'ssl'      => false
            ));
        } catch (\Exception $e) {
            $this->fail();
        }

        $connection->getMessage("");
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $connection = new DefaultImapConnection($this->config);

        $this->assertTrue($connection->connect(array(
            'user'     => 'user',
            'password' => 'password',
            'port'     => 23,
            'host'     => 'imap.host',
            'ssl'      => false
        )));

        $this->assertSame(
            implode(
                $connection->getFolderDelimiter(),
                $this->folderPath->getPath()
            ),
            $connection->selectFolder($this->folderPath)
        );

        $this->assertEquals(
            array('header' => "HEADER", 'body' => "BODY"),
            $connection->getMessage(1)
        );

        $this->assertTrue($connection->setFlags($this->flagCollection));
        $this->assertSame('/', $connection->getFolderDelimiter());
        $this->assertTrue($connection->disconnect());
        $this->assertTrue($connection->isConnected());
    }

}