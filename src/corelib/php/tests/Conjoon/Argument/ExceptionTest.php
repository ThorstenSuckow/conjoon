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

namespace Conjoon\Argument;

/**
 * @see Conjoon\Argument\Exception
 */
require_once 'Conjoon/Argument/Exception.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException Conjoon_Argument_Exception
     */
    public function testException()
    {
        throw new \Conjoon_Argument_Exception();
    }

}
