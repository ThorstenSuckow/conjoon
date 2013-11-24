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


namespace Conjoon\Mail\Client\Message\Strategy;

/**
 * @see  \Conjoon\Mail\Client\Message\Strategy\ReadableStrategyResult
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/ReadableStrategyResult.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ReadableStrategyResultTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected.
     */
    public function testOk() {

        $result = new ReadableStrategyResult("body", true, false);

        $this->assertSame("body", $result->getBody());
        $this->assertTrue($result->hasExternalResources());
        $this->assertFalse($result->areExternalResourcesLoaded());

        $result = new ReadableStrategyResult("body", true, true);

        $this->assertSame("body", $result->getBody());
        $this->assertTrue($result->hasExternalResources());
        $this->assertTrue($result->areExternalResourcesLoaded());

        $result = new ReadableStrategyResult("body", true);

        $this->assertSame("body", $result->getBody());
        $this->assertTrue($result->hasExternalResources());
        $this->assertFalse($result->areExternalResourcesLoaded());

        $result = new ReadableStrategyResult("body");

        $this->assertSame("body", $result->getBody());
        $this->assertFalse($result->hasExternalResources());
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Message\NoExternalResourcesAvailableException
     */
    public function testAreExternalResourcesLoadedException() {

        $result = new ReadableStrategyResult("body", false, false);

        $this->assertSame("body", $result->getBody());
        $result->areExternalResourcesLoaded();

    }

    /**
     * @expectedException \Conjoon\Mail\Client\Message\NoExternalResourcesAvailableException
     */
    public function testAreExternalResourcesLoadedException_NoArgs() {

        $result = new ReadableStrategyResult("body");

        $this->assertSame("body", $result->getBody());
        $result->areExternalResourcesLoaded();
    }
}
