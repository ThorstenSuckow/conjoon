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

namespace Conjoon\Mail\Client\Message;

/**
 * @see \Conjoon\Mail\Client\Message\NoExternalResourcesAvailableException
 */
require_once 'Conjoon/Mail/Client/Message/NoExternalResourcesAvailableException.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class NoExternalResourcesAvailableExceptionTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException \Conjoon\Mail\Client\Message\NoExternalResourcesAvailableException
     *
     * @return void
     */
    public function testException()
    {
        throw new NoExternalResourcesAvailableException();
    }

}
