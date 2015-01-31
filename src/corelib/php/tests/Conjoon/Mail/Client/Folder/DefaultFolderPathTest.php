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
 * @see Conjoon\Mail\Client\Folder\DefaultFolderPath
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultFolderPath.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFolderPathTest extends \PHPUnit_Framework_TestCase {



    protected $_input = array();

    protected $_inputArray = array();

    /**
     * Creates a new Conjoon_Mail_Client_Folder_DefaultClientMailboxFolderPath object for each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->_input = array(
            '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
            => array(
                'path'    => array('INBOXtttt', 'rfwe2', 'New folder (7)'),
                'nodeId'  => 'New folder (7)',
                'rootId'  => 79
            ),
            '["root"]'
            => array(
                'path'    => array(),
                'nodeId'  => null,
                'rootId'  => null
            )
        );

    }

    public function testConstructString()
    {
        foreach ($this->_input as $input => $output) {

            $path = new DefaultFolderPath(
                $input
            );

            $this->assertEquals(
                $output,
                $path->__toArray()
            );

            $this->assertEquals($output['path'],   $path->getPath());
            $this->assertEquals($output['nodeId'], $path->getNodeId());
            $this->assertEquals($output['rootId'], $path->getRootId());
        }

        $this->assertTrue(
            is_string($path->__toString())
        );

    }

}
