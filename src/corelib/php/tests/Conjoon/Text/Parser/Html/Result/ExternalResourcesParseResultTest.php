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


namespace Conjoon\Text\Parser\Html\Result;

/**
 * @see \Conjoon\Text\Parser\Html\ExternalResourcesParseResult
 */
require_once 'Conjoon/Text/Parser/Html/Result/ExternalResourcesParseResult.php';

use \Conjoon\Text\Parser\Html\Result\ExternalResourcesParseResult;

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ExternalResourcesParseResultTest extends \PHPUnit_Framework_TestCase {


    /**
     * @inheritdoc
     */
    public function setUp() {
        parent::setUp();
    }

    /**
     * @inheritdoc
     */
    public function tearDown() {
        parent::tearDown();
    }

// +---------------------------------------------------------------------------
// | Tests
// +---------------------------------------------------------------------------

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testInvalidConstructorArgument() {
        new ExternalResourcesParseResult('true');
    }

    /**
     * Ensure everything works as expected.
     */
    public function testOk() {

        // external resources exist
        $result = new ExternalResourcesParseResult(true);
        $data = $result->getData();
        $this->assertTrue(array_key_exists('externalResources', $data));
        $this->assertTrue($data['externalResources']);

        // external resources don't exist
        $result = new ExternalResourcesParseResult(false);
        $data = $result->getData();
        $this->assertTrue(array_key_exists('externalResources', $data));
        $this->assertFalse($data['externalResources']);

    }


}
