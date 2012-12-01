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

namespace Conjoon\Mail\Client\Message\Flag;

/**
 * @see Conjoon_Mail_Client_Message_Flag_FolderFlagCollection
 */
require_once 'Conjoon/Mail/Client/Message/Flag/FolderFlagCollection.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class FolderFlagCollectionTest extends \PHPUnit_Framework_TestCase {

    protected $_folder;

    protected $_flags;

    protected function setUp()
    {
        $this->_folder = new \Conjoon\Mail\Client\Folder\Folder(
            new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
            )
        );

        $this->_flags =
            new DefaultFlagCollection(
                '[{"id":"173","isRead":false},{"id":"172","isRead":true}]'
            );
    }


    /**
     * Ensures everything works as expected
     *
     * @return void
     */
    public function testOk()
    {
        $collection = new FolderFlagCollection(
            $this->_flags, $this->_folder
        );

        $this->assertSame($this->_folder, $collection->getFolder());
        $this->assertSame($this->_flags, $collection->getFlagCollection());
    }

}
