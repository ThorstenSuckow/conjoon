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


namespace Conjoon\Lang;

/**
 * @see Conjoon\Lang\ClassNotFoundException
 */
require_once 'Conjoon/Lang/ClassNotFoundException.php';


/**
 * @package Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ClassNotFoundExceptionTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException \Conjoon\Lang\ClassNotFoundException
     *
     * @return void
     */
    public function testException()
    {
        throw new ClassNotFoundException();
    }

}
