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
 * @see Conjoon_Exception
 */
require_once 'Conjoon/Exception.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_ExceptionTest extends PHPUnit_Framework_TestCase {



// +---------------------------------------------------------------------------
// | Tests
// +---------------------------------------------------------------------------

    public function testGetPrevious()
    {
        $e = new Exception();

        $ce = new Conjoon_Exception("test", 1, $e);

        $this->assertEquals(1,      $ce->getCode());
        $this->assertEquals("test", $ce->getMessage());

        $this->assertSame($e, $ce->getPrevious());

    }


}
