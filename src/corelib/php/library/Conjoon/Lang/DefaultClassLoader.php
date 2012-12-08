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