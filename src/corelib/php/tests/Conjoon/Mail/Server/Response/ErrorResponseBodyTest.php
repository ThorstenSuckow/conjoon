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
 * @see ErrorResponseBody
 */
require_once 'Conjoon/Mail/Server/Response/ErrorResponseBody.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ErrorResponseBodyTest extends \PHPUnit_Framework_TestCase {


    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructException()
    {
        new ErrorResponseBody(array());
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        new ErrorResponseBody();

        new ErrorResponseBody("");

        $responseBody = new ErrorResponseBody("response");

        $this->assertSame(
            "response",
            $responseBody->getText()
        );

    }

}
