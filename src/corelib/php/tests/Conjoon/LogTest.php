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
 * @see Conjoon_Log
 */
require_once 'Conjoon/Log.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_LogTest extends PHPUnit_Framework_TestCase {

    /**
     *
     * @return void
     */
    public function setUp()
    {

    }

    /**
     *
     * @return void
     */
    public function tearDown()
    {

    }

// +---------------------------------------------------------------------------
// | Tests
// +---------------------------------------------------------------------------


    /**
     * @ticket CN-849
     */
    public function testIsConfigured() {

        $this->assertFalse(Conjoon_Log::isConfigured());

        Conjoon_Log::init(array(
            'enabled' => false
        ));

        $this->assertTrue(Conjoon_Log::isConfigured());
    }

}
