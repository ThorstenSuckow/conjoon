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


namespace Conjoon\Mail\Client\Folder;

/**
 * @see \Conjoon\Mail\Client\Folder\Folder
 */
require_once 'Conjoon/Mail/Client/Folder/Folder.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ClientMailFolderTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $folder = new Folder(
            new DefaultFolderPath(
                '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
            )
        );

        $this->assertEquals(
            array("INBOXtttt", "rfwe2", "New folder (7)"), $folder->getPath()
        );

        $this->assertEquals(
            "79", $folder->getRootId()
        );

        $this->assertTrue(
            is_string($folder->__toString())
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testNodeId()
    {
        $folder = new Folder(
            new DefaultFolderPath(
                '["root", "79"]'
            )
        );

        $this->assertSame(
            array(), $folder->getPath()
        );

        $this->assertEquals(
            "79", $folder->getRootId()
        );

       // $this->assertSame(
       //     null, $folder->getNodeId()
       // );
    }



}
