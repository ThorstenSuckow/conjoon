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
 * @see DefaultMessageServiceFacade
 */
require_once 'Conjoon/Mail/Client/Service/DefaultMessageServiceFacade.php';

/**
 * @see \Conjoon\Mail\Server\Protocol\ProtocolTestCase
 */
require_once dirname(__FILE__) . '/../../Server/Protocol/ProtocolTestCase.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMessageServiceFacadeTest extends
    \Conjoon\Mail\Server\Protocol\ProtocolTestCase {


    public function testOk()
    {
        $protocol = new \Conjoon\Mail\Server\Protocol\DefaultProtocol(
            $this->protocolAdaptee
        );

        $defaultServer = new \Conjoon\Mail\Server\DefaultServer($protocol);

        $messageFacade = new DefaultMessageServiceFacade($defaultServer);
        $result = $messageFacade->setFlagsForMessagesInFolder(
            '[{"id":"56","isRead":true}]', '["root","1","2"]', $this->user

        );

        $this->assertTrue($result instanceof ServiceResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testGetMessage()
    {
        $protocol = new \Conjoon\Mail\Server\Protocol\DefaultProtocol(
            $this->protocolAdaptee
        );

        $defaultServer = new \Conjoon\Mail\Server\DefaultServer($protocol);

        $messageFacade = new DefaultMessageServiceFacade($defaultServer);
        $result = $messageFacade->getMessage(
            "1", '["root","1","2"]', $this->user

        );

        $this->assertTrue($result instanceof ServiceResult);
        $this->assertTrue($result->isSuccess());
    }


    public function testGetAttachment()
    {
        $protocol = new \Conjoon\Mail\Server\Protocol\DefaultProtocol(
            $this->protocolAdaptee
        );

        $defaultServer = new \Conjoon\Mail\Server\DefaultServer($protocol);

        $messageFacade = new DefaultMessageServiceFacade($defaultServer);
        $result = $messageFacade->getAttachment(
            "32234234234324234", "1", '["root","1","2"]', $this->user

        );

        $this->assertTrue($result instanceof ServiceResult);
        $this->assertTrue($result->isSuccess());
    }
}
