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


namespace Conjoon\Mail\Server\Protocol;

/**
 * @see DefaultProtocol
 */
require_once 'Conjoon/Mail/Server/Protocol/DefaultProtocol.php';

require_once dirname(__FILE__) . '/ProtocolTestCase.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultProtocolTest extends ProtocolTestCase {

    protected $protocol;

    protected $failProtocol;

    protected function setUp()
    {
        parent::setUp();

        $this->protocol = new DefaultProtocol($this->protocolAdaptee);

        $this->failProtocol = new DefaultProtocol($this->failProtocolAdaptee);
    }


    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $this->assertTrue(
            $this->protocol->setFlags(
                $this->folderFlagCollection,
                $this->user
            )
                instanceof
                \Conjoon\Mail\Server\Protocol\DefaultResult\SetFlagsResult
        );

        $this->assertTrue(
            $this->failProtocol->setFlags(
                $this->folderFlagCollection,
                $this->user
            )
                instanceof
                \Conjoon\Mail\Server\Protocol\DefaultResult\ErrorResult
        );

    }

}
