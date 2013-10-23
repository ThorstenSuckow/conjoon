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
 * @see Conjoon\Data\Entity\SimpleDataEntity
 */
require_once 'Conjoon/Data/Entity/SimpleDataEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleDataEntityTest extends \PHPUnit_Framework_TestCase {


    public function testOk()
    {
        $sd = new SimpleDataEntity();

        $val = $sd->__toString();

        $this->assertFalse(empty($val));

        $this->assertTrue(is_string($val));
    }

}
