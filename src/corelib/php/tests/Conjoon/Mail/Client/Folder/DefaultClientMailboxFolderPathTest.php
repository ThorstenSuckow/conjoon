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
 * @see Conjoon_Mail_Client_Folder_DefaultClientMailboxFolderPath
 */
require_once 'Conjoon/Mail/Client/Folder/DefaultClientMailboxFolderPath.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Client_Folder_DefaultClientMailboxFolderPathTest extends PHPUnit_Framework_TestCase {



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

            $path = new Conjoon_Mail_Client_Folder_DefaultClientMailboxFolderPath(
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

    }

}