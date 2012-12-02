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
 * @see Response
 */
require_once 'Conjoon/Mail/Server/Response/DefaultResponse.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultResponseTest extends \PHPUnit_Framework_TestCase {


    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructorWithException()
    {
        new DefaultResponse(
            new \Conjoon\Mail\Server\Request\SimpleRequest(array()),
            new SimpleResponseBody
        );
    }


    /**
     * Ensures everything works as expected.
     */
    public function testIsSuccess()
    {
        $response = new DefaultResponse(
            new \Conjoon\Mail\Server\Request\SimpleRequest(array()),
            new SuccessResponseBody
        );

        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
    }

    /**
     * Ensures everything works as expected.
     */
    public function testIsError()
    {
        $response = new DefaultResponse(
            new \Conjoon\Mail\Server\Request\SimpleRequest(array()),
            new ErrorResponseBody
        );

        $this->assertFalse($response->isSuccess());
        $this->assertTrue($response->isError());
    }

    /**
     * Ensures everything works as expected.
     */
    public function testGetRequest()
    {
        $simpleRequest = new \Conjoon\Mail\Server\Request\SimpleRequest(array());
        $response = new DefaultResponse(
            $simpleRequest, new ErrorResponseBody
        );

        $this->assertSame($simpleRequest, $response->getRequest());
    }

    /**
     * Ensures everything works as expected.
     */
    public function testGetResponseBody()
    {
        $simpleRequest = new \Conjoon\Mail\Server\Request\SimpleRequest(array());
        $responseBody = new ErrorResponseBody();
        $response = new DefaultResponse(
            $simpleRequest, $responseBody
        );

        $this->assertSame($responseBody, $response->getResponseBody());
    }

}
