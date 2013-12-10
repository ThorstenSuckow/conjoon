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
