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
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class InvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testException()
    {
        throw new InvalidArgumentException();
    }

    /**
     * @expectedException \Conjoon_Argument_Exception
     */
    public function testExceptionParent()
    {
        throw new InvalidArgumentException();
    }

}
