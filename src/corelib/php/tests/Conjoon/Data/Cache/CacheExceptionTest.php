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


namespace Conjoon\Data\Cache;

/**
 * @see Conjoon\Data\Cache\CacheException
 */
require_once 'Conjoon/Data/Cache/CacheException.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Cache
 * @subpackage UnitTests
 * @group      Conjoon_Cache
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class CacheExceptionTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException \Conjoon\Data\Cache\CacheException
     *
     * @return void
     */
    public function testException()
    {
        throw new CacheException();
    }

}
