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
 * @see \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/DefaultPlainReadableStrategy.php';

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

    protected $mailAccountRepository;

    protected $mailFolderRepository;

    protected $plainReadabelStrategy;


    protected function getMessageServiceFacade($server, $mailAccountRepository, $mailFolderRepository) {

        return  new DefaultMessageServiceFacade(
            $server, $mailAccountRepository, $mailFolderRepository);

    }

    protected function setUp()
    {
        parent::setUp();


        $this->plainReadableStrategy = new \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy;

        $this->mailAccountRepository = new DoctrineMailAccountRepositoryMock();

        $this->mailFolderRepository = new DoctrineMailFolderRepositoryMock();

    }


    public function testOk()
    {
        $protocol = new \Conjoon\Mail\Server\Protocol\DefaultProtocol(
            $this->protocolAdaptee
        );

        $defaultServer = new \Conjoon\Mail\Server\DefaultServer($protocol);

        $messageFacade = $this->getMessageServiceFacade($defaultServer,
            $this->mailAccountRepository, $this->mailFolderRepository);
        $result = $messageFacade->setFlagsForMessagesInFolder(
            '[{"id":"56","isRead":true}]', '["root","1","2"]', $this->user

        );

        $this->assertTrue($result instanceof ServiceResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testGetUnformattedMessage()
    {
        $protocol = new \Conjoon\Mail\Server\Protocol\DefaultProtocol(
            $this->protocolAdaptee
        );

        $defaultServer = new \Conjoon\Mail\Server\DefaultServer($protocol);

        $messageFacade = $this->getMessageServiceFacade($defaultServer,
            $this->mailAccountRepository, $this->mailFolderRepository);
        $result = $messageFacade->getUnformattedMessage(
            "1", '["root","1","2"]', $this->user
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

        $messageFacade = $this->getMessageServiceFacade($defaultServer,
            $this->mailAccountRepository, $this->mailFolderRepository);
        $result = $messageFacade->getMessage(
            "1", '["root","1","2"]', $this->user,  $this->plainReadableStrategy
        );

        $this->assertTrue($result instanceof
            \Conjoon\Mail\Client\Service\ServiceResult\GetMessageServiceResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testGetMessageForForwarding()
    {
        $protocol = new \Conjoon\Mail\Server\Protocol\DefaultProtocol(
            $this->protocolAdaptee
        );

        $defaultServer = new \Conjoon\Mail\Server\DefaultServer($protocol);

        $messageFacade = $this->getMessageServiceFacade($defaultServer,
            $this->mailAccountRepository, $this->mailFolderRepository);
        $result = $messageFacade->getMessageForReply(
            "1", '["root","1","2"]', $this->user

        );

        $this->assertTrue($result instanceof ServiceResult);
        $this->assertTrue($result->isSuccess());

        // $replyAll-> true
        $result = $messageFacade->getMessageForForwarding(
            "1", '["root","1","2"]', $this->user, true
        );

        $this->assertTrue($result instanceof ServiceResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testGetMessageForReply()
    {
        $protocol = new \Conjoon\Mail\Server\Protocol\DefaultProtocol(
            $this->protocolAdaptee
        );

        $defaultServer = new \Conjoon\Mail\Server\DefaultServer($protocol);

        $messageFacade =$this->getMessageServiceFacade($defaultServer,
            $this->mailAccountRepository, $this->mailFolderRepository);
        $result = $messageFacade->getMessageForReply(
            "1", '["root","1","2"]', $this->user

        );

        $this->assertTrue($result instanceof ServiceResult);
        $this->assertTrue($result->isSuccess());

        // $replyAll-> true
        $result = $messageFacade->getMessageForReply(
            "1", '["root","1","2"]', $this->user, true
        );

        $this->assertTrue($result instanceof ServiceResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testGetMessageForComposing()
    {
        $protocol = new \Conjoon\Mail\Server\Protocol\DefaultProtocol(
            $this->protocolAdaptee
        );

        $defaultServer = new \Conjoon\Mail\Server\DefaultServer($protocol);

        $messageFacade = $this->getMessageServiceFacade($defaultServer,
            $this->mailAccountRepository, $this->mailFolderRepository);
        $result = $messageFacade->getMessageForComposing(
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

        $messageFacade = $this->getMessageServiceFacade(
            $defaultServer,
            $this->mailAccountRepository, $this->mailFolderRepository
        );

        $result = $messageFacade->getAttachment(
            "32234234234324234", "1", '["root","1","2"]', $this->user

        );

        $this->assertTrue($result instanceof ServiceResult);
        $this->assertTrue($result->isSuccess());
    }
}


class DoctrineMailAccountRepositoryMock extends \Conjoon\Data\Repository\Mail\DoctrineMailAccountRepository {

    public function __construct()
    {

    }

    public function getStandardMailAccount(\Conjoon\User\User $user)
    {
        return new \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity();
    }

    public function getMailAccounts(\Conjoon\User\User $user)
    {
        return array();
    }

}

class DoctrineMailFolderRepositoryMock extends \Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository {

    public function __construct()
    {

    }

}
