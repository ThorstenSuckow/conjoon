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
 * Interface all class loaders have to implement.
 *
 * @category   Conjoon_Lang
 * @package    Lang
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ClassLoader  {

    /**
     * Tries to load the class specified in $className.
     *
     * @param string $className
     *
     * @throws \Conjoon\Io\FileNotFoundException
     * @throws \Conjoon\Lang\ClassNotFoundException
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function loadClass($className);

}