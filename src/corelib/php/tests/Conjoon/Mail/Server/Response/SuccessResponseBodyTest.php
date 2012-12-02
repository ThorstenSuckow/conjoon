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
require_once 'Conjoon/Mail/Server/Response/SuccessResponseBody.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SuccessResponseBodyTest extends \PHPUnit_Framework_TestCase {


    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructException()
    {
        new SuccessResponseBody(array());
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        new SuccessResponseBody();

        new SuccessResponseBody("");

        $responseBody = new SuccessResponseBody("response");

        $this->assertSame(
            "response",
            $responseBody->getText()
        );

    }

}
