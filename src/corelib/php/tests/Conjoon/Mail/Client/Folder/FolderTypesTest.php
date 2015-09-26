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
 * @see \Conjoon\Mail\Client\Folder\FolderTypes
 */
require_once 'Conjoon/Mail/Client/Folder/FolderTypes.php';

/**
 * @see \Conjoon\Data\Meta\Mail\FolderTypes
 */
require_once 'Conjoon/Data/Meta/Mail/FolderTypes.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class FolderTypesTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $types = FolderTypes::getFolderTypesForFirstLevelChildFolders();
        $this->assertSame(1, count($types));
        $this->assertTrue(
            in_array(\Conjoon\Data\Meta\Mail\FolderTypes::FOLDER, $types));

        $types = FolderTypes::getFirstLevelFolderTypes();
        $this->assertSame(6, count($types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::INBOX, $types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::DRAFT, $types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::SENT, $types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::TRASH, $types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::OUTBOX, $types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::SPAM, $types));

        $types = FolderTypes::getFolderTypes();
        $this->assertSame(7, count($types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::INBOX, $types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::DRAFT, $types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::SENT, $types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::TRASH, $types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::OUTBOX, $types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::SPAM, $types));
        $this->assertTrue(in_array(\Conjoon\Data\Meta\Mail\FolderTypes::FOLDER, $types));
    }

}