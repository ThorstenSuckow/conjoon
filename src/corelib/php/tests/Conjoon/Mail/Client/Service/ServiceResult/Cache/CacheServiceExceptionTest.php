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


namespace Conjoon\Mail\Client\Service\ServiceResult\Cache;

/**
 * @see \Conjoon\Mail\Client\Service\ServiceResult\Cache\CacheServiceException
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/CacheServiceException.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Service
 * @subpackage UnitTests
 * @group      Conjoon_Service
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class CacheServiceExceptionTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException \Conjoon\Mail\Client\Service\ServiceResult\Cache\CacheServiceException
     *
     * @return void
     */
    public function testException()
    {
        throw new CacheServiceException();
    }

}
