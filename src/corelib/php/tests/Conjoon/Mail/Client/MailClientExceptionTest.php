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
 * @see Conjoon_Mail_Client_MailClientException
 */
require_once 'Conjoon/Mail/Client/MailClientException.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Client_MailClientExceptionTest
    extends PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException Conjoon_Mail_Client_MailClientException
     *
     * @return void
     */
    public function testException()
    {
        throw new Conjoon_Mail_Client_MailClientException();
    }

}
