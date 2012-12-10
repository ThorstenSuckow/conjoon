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
 * @see SimpleProtocolAdaptee
 */
require_once 'Conjoon/Mail/Server/Protocol/SimpleProtocolAdaptee.php';


require_once dirname(__FILE__) . '/ProtocolTestCase.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleProtocolAdapteeTest extends ProtocolTestCase {


    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $this->assertTrue(
            $this->protocolAdaptee->setFlags(
                $this->folderFlagCollection,
                $this->user
            )
            instanceof
            \Conjoon\Mail\Server\Protocol\DefaultResult\SetFlagsResult
        );


        $this->assertTrue(
            $this->protocolAdaptee->getMessage(
                new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                    $this->folderFlagCollection->getFolder(), 1
                ),
                $this->user
            )
                instanceof
                \Conjoon\Mail\Server\Protocol\DefaultResult\GetMessageResult
        );

    }

}
