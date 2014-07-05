<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

use Conjoon\Lang\ClassNotFoundException,
    Conjoon\Io\FileNotFoundException,
    Conjoon\Argument\ArgumentCheck;

/**
 * @see Conjoon\Lang\ClassLoader
 */
require_once 'Conjoon/Lang/ClassLoader.php';

/**
 * @see Conjoon\Lang\ClassNotFoundException
 */
require_once 'Conjoon/Lang/ClassNotFoundException.php';

/**
 * @see Conjoon\Io\FileNotFoundException
 */
require_once 'Conjoon/Io/FileNotFoundException.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * Default implementation for \Conjoon\Lang\ClassLoader.
 *
 * @category   Conjoon_Lang
 * @package    Lang
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultClassLoader implements ClassLoader {

    /**
     * Tries to load the class specified in $className.
     *
     * @param string $className
     *
     * @return boolean
     *
     * @throws \Conjoon\Io\FileNotFoundException
     * @throws \Conjoon\Lang\ClassNotFoundException
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function loadClass($className)
    {
        $data = array('className' => $className);

        ArgumentCheck::check(array(
            'className' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $data);

        $className = $data['className'];

        if (!class_exists($className, false)) {
            $fileName = str_replace('\\', '/', ltrim($className, '\\')) . '.php';

            $res = @include_once $fileName;

            if ($res === false) {
                throw new FileNotFoundException(
                    "Could not find file \"$fileName\" for class \"$className\""
                );
            }

            if (!class_exists($className, false)) {
                throw new ClassNotFoundException(
                    "Could not find class \"$className\" in \"$fileName\""
                );
            }
        }

        return true;
    }


}