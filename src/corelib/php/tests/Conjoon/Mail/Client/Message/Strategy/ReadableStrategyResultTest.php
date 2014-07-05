<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
