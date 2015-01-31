<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
