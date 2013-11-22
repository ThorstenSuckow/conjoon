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


namespace Conjoon\Net;

/**
 * @see Conjoon\Net\Environment
 */
require_once 'Conjoon/Net/Environment.php';


/**
 * @category   Conjoon
 * @package    Conjoon\Net
 * @subpackage UnitTests
 * @group      Conjoon\Net
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException \Conjoon\Net\Exception
     */
    public function testGetCurrentUriBase()
    {
        $env = new \Conjoon\Net\Environment;

        $this->assertSame($env->getCurrentUriBase(), '');
    }

}
