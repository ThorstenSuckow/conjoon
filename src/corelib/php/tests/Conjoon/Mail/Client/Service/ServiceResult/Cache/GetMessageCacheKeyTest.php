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


namespace Conjoon\Mail\Client\Service\ServiceResult\Cache;

/**
 * @see GetMessageCacheKey
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCacheKey.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Service
 * @subpackage UnitTests
 * @group      Conjoon_Service
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class GetMessageCacheKeyTest extends \PHPUnit_Framework_TestCase {

    /**
     * Ensures everything works as expected
     */
    public function testOk() {

        $key = new GetMessageCacheKey("key");

        $this->assertSame("key", $key->getValue());
        $this->assertTrue(is_string($key->__toString()));
    }


    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructWithException_1() {
        new GetMessageCacheKey(1);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructWithException_2() {
        new GetMessageCacheKey("");
    }

}
