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

namespace Conjoon\Data\EntityCreator;

/**
 * @see Conjoon\Data\EntityCreator\EntityCreatorException
 */
require_once 'Conjoon/Data/EntityCreator/EntityCreatorException.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class EntityCreatorExceptionExceptionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException Conjoon\Data\EntityCreator\EntityCreatorException
     */
    public function testException()
    {
        throw new \Conjoon\Data\EntityCreator\EntityCreatorException();
    }

}
