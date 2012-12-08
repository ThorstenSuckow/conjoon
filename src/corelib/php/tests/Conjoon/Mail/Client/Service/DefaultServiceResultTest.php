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


namespace Conjoon\Mail\Client\Service;

/**
 * @see DefaultServiceResult
 */
require_once 'Conjoon/Mail/Client/Service/DefaultServiceResult.php';

/**
 *@see \Conjoon\Mail\Server\Request\SimpleRequest
 */
require_once dirname(__FILE__) . '/../../Server/Request/SimpleRequest.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultServiceResultTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     */
    public function testWithInvalidArgument()
    {
        $result = new DefaultServiceResult("test");

        $this->assertFalse($result->isSuccess());
        $this->assertTrue(is_array($result->getData()));

        $resArr = $result->toArray();

        $this->assertTrue(array_key_exists('success', $resArr));
        $this->assertTrue(array_key_exists('data',    $resArr));
        $this->assertTrue(array_key_exists('message', $resArr['data']));
    }

    /**
     * Ensures everything works as expected
     */
    public function testWithException()
    {
        $result = new DefaultServiceResult(
            new \Exception('message', 1, new \Exception()));

        $this->assertFalse($result->isSuccess());
        $this->assertTrue(is_array($result->getData()));

        $resArr = $result->toArray();

        $this->assertTrue(array_key_exists('success', $resArr));
        $this->assertTrue(array_key_exists('data',    $resArr));
        $this->assertTrue(array_key_exists('message', $resArr['data']));
        $this->assertTrue(array_key_exists('exception', $resArr['data']));
        $this->assertTrue(array_key_exists('code', $resArr['data']));
        $this->assertTrue(array_key_exists('previous', $resArr['data']));
        $this->assertTrue(is_array($resArr['data']['previous']));
    }

    /**
     * Ensures everything works as expected
     */
    public function testWithResponse()
    {
        $request = new \Conjoon\Mail\Server\Request\SimpleRequest(array());

        $responseBody = new \Conjoon\Mail\Server\Response\DefaultResponseBody(
            array('data' => 'data'));

        $response = new \Conjoon\Mail\Server\Response\DefaultResponse(
            $request,  $responseBody, array('status' => 200)
        );

        $result = new DefaultServiceResult($response);

        $this->assertTrue($result->isSuccess());
        $this->assertEquals($responseBody->getData(), $result->getData());

        $arr = $result->toArray();

        $this->assertSame(json_encode($arr), $result->toJson());

    }

}