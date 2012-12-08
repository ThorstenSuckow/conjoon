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


namespace Conjoon\Io;

/**
 * @see Conjoon\Io\FileNotFoundException
 */
require_once 'Conjoon/Io/FileNotFoundException.php';


/**
 * @package Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class FileNotFoundExceptionTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException \Conjoon\Io\FileNotFoundException
     *
     * @return void
     */
    public function testException()
    {
        throw new FileNotFoundException();
    }

}
