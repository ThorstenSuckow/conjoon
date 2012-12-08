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

namespace Conjoon\Mail\Client\Account;

/**
 * @see Conjoon\Mail\Client\Account\AccountServiceException
 */
require_once 'Conjoon/Mail/Client/Account/AccountServiceException.php';

/**
 * @package Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class AccountServiceExceptionTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException \Conjoon\Mail\Client\Account\AccountServiceException
     *
     * @return void
     */
    public function testException()
    {
        throw new AccountServiceException();
    }

}
