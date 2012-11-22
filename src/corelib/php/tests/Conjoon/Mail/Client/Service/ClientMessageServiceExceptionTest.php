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


/**
 * @see Conjoon_Mail_Client_Service_ClientMessageServiceException
 */
require_once 'Conjoon/Mail/Client/Service/ClientMessageServiceException.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Client_Service_ClientMessageServiceExceptionTest
    extends PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException Conjoon_Mail_Client_Service_ClientMessageServiceException
     *
     * @return void
     */
    public function testException()
    {
        throw new Conjoon_Mail_Client_Service_ClientMessageServiceException();
    }

}
