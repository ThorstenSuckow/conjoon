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
