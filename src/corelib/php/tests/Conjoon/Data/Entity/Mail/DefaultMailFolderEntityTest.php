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

namespace Conjoon\Data\Entity\Mail;

/**
 * @see Conjoon\Data\Entity\Mail\DefaultMailFolderEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultMailFolderEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailFolderEntityTest extends \PHPUnit_Framework_TestCase {

    protected $_input;

    protected $_parent;

    protected function setUp()
    {
        $this->_input = array(
            'id' => "id",
            'name' => "name",
            'isChildAllowed' => true,
            'isLocked' => false,
            'type' => "type",
            'metaInfo' => "metaInfp",
            'isDeleted' => false,
            'parent' => null
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $folder = new DefaultMailFolderEntity();

        $folder->setId($this->_input['id']);
        $this->assertSame($this->_input['id'], $folder->getId());

        $folder->setName($this->_input['name']);
        $this->assertSame($this->_input['name'], $folder->getName());

        $folder->setIsChildAllowed($this->_input['isChildAllowed']);
        $this->assertSame($this->_input['isChildAllowed'], $folder->getIsChildAllowed());

        $folder->setIsLocked($this->_input['isLocked']);
        $this->assertSame($this->_input['isLocked'], $folder->getIsLocked());

        $folder->setType($this->_input['type']);
        $this->assertSame($this->_input['type'], $folder->getType());

        $folder->setMetaInfo($this->_input['metaInfo']);
        $this->assertSame($this->_input['metaInfo'], $folder->getMetaInfo());

        $folder->setIsDeleted($this->_input['isDeleted']);
        $this->assertSame($this->_input['isDeleted'], $folder->getIsDeleted());

        $this->assertSame(0, count($folder->getMailAccounts()));

        $folder->setParent(null);
        $this->assertSame(null, $folder->getParent());
        $fl2 = new DefaultMailFolderEntity();
        $folder->setParent($fl2);
        $this->assertSame($fl2, $folder->getParent());
    }

}