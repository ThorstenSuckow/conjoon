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
 * @see CacheableMessageServiceFacade
 */
require_once 'Conjoon/Mail/Client/Service/CacheableMessageServiceFacade.php';

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
class CacheableMessageServiceFacadeTest extends
    \Conjoon\Mail\Server\Protocol\ProtocolTestCase {



    protected function setUp()
    {
        parent::setUp();


    }


    public function testOk()
    {
        $this->fail();
    }

}
