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
 * @see Conjoon\Net\InvalidContextException
 */
require_once 'Conjoon/Net/InvalidContextException.php';


/**
 * @category   Conjoon
 * @package    Conjoon\Net
 * @subpackage UnitTests
 * @group      Conjoon\Net
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class InvalidContextExceptionTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException \Conjoon\Net\InvalidContextException
     *
     * @return void
     */
    public function testException()
    {
        throw new InvalidContextException();
    }

}
