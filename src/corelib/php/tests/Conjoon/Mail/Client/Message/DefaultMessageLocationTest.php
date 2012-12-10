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


namespace Conjoon\Mail\Client\Message;

/**
 * @see Conjoon\Mail\Client\Message\DefaultMessageLocation
 */
require_once 'Conjoon/Mail/Client/Message/DefaultMessageLocation.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMessageLocationTest extends \PHPUnit_Framework_TestCase {

    protected $folder;

    protected function setUp()
    {
        parent::setUp();

        $this->folder = new \Conjoon\Mail\Client\Folder\Folder(
            new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                '["1", "2"]'
            )
        );
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructor_Exception()
    {
        new DefaultMessageLocation($this->folder, "");
    }

    /**
     * Ensures everything works as expected.
     */
    public function testOk()
    {
        $loc = new DefaultMessageLocation($this->folder, 1);

        $this->assertSame($this->folder, $loc->getFolder());
        $this->assertSame("1", $loc->getMessageId());

    }

}
