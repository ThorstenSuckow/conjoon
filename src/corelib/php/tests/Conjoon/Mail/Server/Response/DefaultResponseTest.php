<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
            new DefaultResponseBody,
            array()
        );
    }


    /**
     * Ensures everything works as expected.
     */
    public function testIsSuccess()
    {
        $response = new DefaultResponse(
            new \Conjoon\Mail\Server\Request\SimpleRequest(array()),
            new DefaultResponseBody,
            array('status' => 200)
        );

        $this->assertSame(200, $response->getStatus());
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
            new DefaultResponseBody,
            array('status' => 100)
        );

        $this->assertSame(100, $response->getStatus());
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
            $simpleRequest, new DefaultResponseBody, array('status' => 200)
        );

        $this->assertSame($simpleRequest, $response->getRequest());
    }

    /**
     * Ensures everything works as expected.
     */
    public function testGetResponseBody()
    {
        $simpleRequest = new \Conjoon\Mail\Server\Request\SimpleRequest(array());
        $responseBody = new DefaultResponseBody();
        $response = new DefaultResponse(
            $simpleRequest, $responseBody, array('status' => 200)
        );

        $this->assertSame($responseBody, $response->getResponseBody());
    }

}
