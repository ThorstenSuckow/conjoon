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
