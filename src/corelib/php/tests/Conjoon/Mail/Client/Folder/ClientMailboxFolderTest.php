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


/**
 * @see Conjoon_Mail_Client_Folder_ClientMailboxFolder
 */
require_once 'Conjoon/Mail/Client/Folder/ClientMailboxFolder.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Client_Folder_ClientMailboxFolderTest extends PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $folder = new Conjoon_Mail_Client_Folder_ClientMailboxFolder(
            new Conjoon_Mail_Client_Folder_DefaultClientMailboxFolderPath(
                '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
            )
        );

        $this->assertEquals(
            array("INBOXtttt", "rfwe2", "New folder (7)"), $folder->getPath()
        );
    }

}