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
 * @see Conjoon\Mail\Client\Message\DefaultAttachmentLocation
 */
require_once 'Conjoon/Mail/Client/Message/DefaultAttachmentLocation.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAttachmentLocationTest extends \PHPUnit_Framework_TestCase {

    protected $messageLocation;

    protected function setUp()
    {
        parent::setUp();

        $this->messageLocation =
            new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
            new \Conjoon\Mail\Client\Folder\Folder(
            new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                '["1", "2"]'
            )), "1"
        );
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructor_Exception()
    {
        new DefaultAttachmentLocation($this->messageLocation, "");
    }

    /**
     * Ensures everything works as expected.
     */
    public function testOk()
    {
        $loc = new DefaultAttachmentLocation($this->messageLocation, 1);

        $this->assertSame($this->messageLocation, $loc->getMessageLocation());
        $this->assertSame("1", $loc->getIdentifier());

    }

}
