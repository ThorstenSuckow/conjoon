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
 * @see Conjoon_Mail_Client_Service_ClientMessageServiceException
 */
require_once 'Conjoon/Mail/Client/Service/MessageServiceException.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class MessageServiceExceptionTest
    extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException \Conjoon\Mail\Client\Service\MessageServiceException
     *
     * @return void
     */
    public function testException()
    {
        throw new MessageServiceException();
    }

}
