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


namespace Conjoon\Mail\Server;

/**
 * @see DefaultServer
 */
require_once 'Conjoon/Mail/Server/DefaultServer.php';

/**
 * @see \Conjoon\Mail\Server\Protocol\ProtocolTestCase
 */
require_once dirname(__FILE__) . '/Protocol/ProtocolTestCase.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultServerTest extends \Conjoon\Mail\Server\Protocol\ProtocolTestCase {

    protected function setUp()
    {
        parent::setUp();
    }



    /**
     * Ensures everything works as expected
     */
    public function testProtocolCommandUnknown()
    {
        $protocol = new Protocol\DefaultProtocol($this->protocolAdaptee);

        $defaultServer = new DefaultServer($protocol);

        $request = new Request\SimpleRequest(array());

        $response = $defaultServer->handle($request);

        $this->assertTrue($response instanceof Response\DefaultResponse);

        $this->assertTrue(is_array($response->getResponseBody()->getData()));

        $this->assertSame(Response\Response::STATUS_CODE_101, $response->getStatus());
    }

    /**
     * Ensures everything works as expected
     */
    public function testProtocolOk()
    {
        $protocol = new Protocol\DefaultProtocol($this->protocolAdaptee);

        $defaultServer = new DefaultServer($protocol);

        $request = new Request\DefaultSetFlagsRequest(array(
            'user'       => $this->user,
            'parameters' => array(
                'folderFlagCollection' => $this->folderFlagCollection
             )
        ));

        $response = $defaultServer->handle($request);

        $this->assertTrue($response instanceof Response\DefaultResponse);
        $this->assertTrue(is_array($response->getResponseBody()->getData()));
        $this->assertSame(Response\Response::STATUS_CODE_200, $response->getStatus());

        $getMessageRequest  = new Request\DefaultGetMessageRequest(array(
            'user'       => $this->user,
            'parameters' => array(
                'messageLocation' => new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                    $this->folderFlagCollection->getFolder(), 1
                )
            )
        ));

        $response = $defaultServer->handle($getMessageRequest);

        $this->assertTrue($response instanceof Response\DefaultResponse);
        $this->assertTrue(is_array($response->getResponseBody()->getData()));
        $this->assertSame(Response\Response::STATUS_CODE_200, $response->getStatus());

    }

    /**
     * Ensures everything works as expected
     */
    public function testProtocolAdapteeFail()
    {
        $protocol = new Protocol\DefaultProtocol($this->failProtocolAdaptee);

        $defaultServer = new DefaultServer($protocol);

        $request = new Request\DefaultSetFlagsRequest(array(
            'user'       => $this->user,
            'parameters' => array(
                'folderFlagCollection' => $this->folderFlagCollection
            )
        ));

        $response = $defaultServer->handle($request);

        $this->assertTrue($response instanceof Response\DefaultResponse);

        $this->assertTrue(is_array($response->getResponseBody()->getData()));

        $this->assertSame(Response\Response::STATUS_CODE_100, $response->getStatus());
    }

}
