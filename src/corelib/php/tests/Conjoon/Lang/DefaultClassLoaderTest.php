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
 * @see Conjoon\Lang\DefaultClassLoader
 */
require_once 'Conjoon/Lang/DefaultClassLoader.php';


/**
 * @package Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultClassLoaderTest extends \PHPUnit_Framework_TestCase {

    protected $classLoader;

    protected function setUp()
    {
        $this->classLoader = new DefaultClassLoader();
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testLoadClass_InvalidArgument()
    {
        $this->classLoader->loadClass("");
    }

    /**
     * @expectedException \Conjoon\Io\FileNotFoundException
     */
    public function testLoadClass_MissingFile()
    {
        $this->classLoader->loadClass("bla");
    }

    /**
     * @expectedException \Conjoon\Lang\ClassNotFoundException
     */
    public function testLoadClass_MissingClass()
    {
        $this->classLoader->loadClass("\Conjoon\Lang\ClassLoaderTestMissingClass");
    }

    /**
     * Ensures everything works as expected.
     */
    public function testOk()
    {
        $this->assertTrue(
            $this->classLoader->loadClass(
                "\Conjoon\Lang\DefaultClassLoader"
        ));

        $this->assertTrue(
            $this->classLoader->loadClass(
                "\Conjoon\Lang\ClassLoaderTestExistingClass"
        ));
    }

}
