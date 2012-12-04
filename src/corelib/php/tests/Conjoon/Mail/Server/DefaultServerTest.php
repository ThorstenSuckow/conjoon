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
