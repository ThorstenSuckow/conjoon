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


namespace Conjoon\Mail\Server\Request;

/**
 * @see Request
 */
require_once dirname(__FILE__) . '/SimpleRequest.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleRequestTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $request = new SimpleRequest(array());

        $this->assertSame("test", $request->getProtocolCommand());
    }

}
