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


namespace Conjoon\Mail\Server\Response;

/**
 * @see SuccessResponseBody
 */
require_once dirname(__FILE__) . '/SimpleResponseBody.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleResponseBodyTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        new SimpleResponseBody();

        new SimpleResponseBody(array());

        $arr = array("response");

        $responseBody = new SimpleResponseBody($arr);

        $this->assertSame(
            $arr,
            $responseBody->getData()
        );

    }

}
