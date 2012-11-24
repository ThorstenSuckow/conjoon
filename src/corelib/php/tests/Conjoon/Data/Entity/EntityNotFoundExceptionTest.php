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

namespace Conjoon\Data\Entity;

/**
 * @see Conjoon\Data\Entity\EntityNotFoundException
 */
require_once 'Conjoon/Data/Entity/EntityNotFoundException.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class EntityNotFoundExceptionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException Conjoon\Data\Entity\EntityNotFoundException
     */
    public function testException()
    {
        throw new \Conjoon\Data\Entity\EntityNotFoundException();
    }

}
