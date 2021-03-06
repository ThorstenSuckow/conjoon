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

/**
 * Conjoon_BeanContext
 */
require_once 'Conjoon/BeanContext.php';

/**
 * Conjoon_BeanContext_Exception
 */
require_once 'Conjoon/BeanContext/Exception.php';

/**
 * Static class with utility methods for working on classes implementing the
 * Conjoon_BeanContext-interface and overall introspection of classes/object-instances.
 *
 * @category   Conjoon
 * @package    Conjoon_BeanContext
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_BeanContext_Inspector {

    /**
     * @const string
     */
    const GETTER = 'get';

    /**
     * @const string
     */
    const SETTER = 'set';

    /**
     * @array
     */
    private static $cache = array();

    /**
     * Returns the reflection class for the object out of the cache.
     *
     * @param Object
     *
     * @return ReflectionClass
     */
    private static function getReflectionClass($object)
    {
        $class = get_class($object);
        if (!isset(self::$cache[$class]['class'])) {
            self::$cache[$class]['class'] = new ReflectionClass($object);
        }

        return self::$cache[$class]['class'];
    }

    /**
     * Returns reflection properties for the class the object represents
     * out of the cache.
     *
     * @param Object
     *
     * @return Array|ReflectionProperty
     */
    private static function getReflectionProperties($object)
    {
        $class = get_class($object);

        if (!isset(self::$cache[$class]['properties'])) {
            $refClass = self::getReflectionClass($object);
            self::$cache[$class]['properties'] = $refClass->getProperties();
        }

        return self::$cache[$class]['properties'];
    }

    /**
     * Returns reflection methods for the class the object represents
     * out of the cache.
     *
     * @param Object
     *
     * @return Array|ReflectionMethod
     */
    private static function getReflectionMethods($object)
    {
        $class = get_class($object);

        if (!isset(self::$cache[$class]['methods'])) {
            $refClass = self::getReflectionClass($object);
            self::$cache[$class]['methods'] = $refClass->getMethods();
        }

        return self::$cache[$class]['methods'];
    }

    /**
     * Checks wether a property is transient. A property is transient,
     * if an underscore is part of the property-name. This method
     * will also check if a property with the same name is available
     * that starts with an underscore.
     *
     * @param string $propertyName
     * @param Object $object
     *
     * @return boolean true, if the property is transient, otherwise false
     */
    private static function isTransient($propertyName, $object)
    {
        if (strpos($propertyName, '_') !== false) {
            return true;
        }

        $refClass = self::getReflectionClass($object);

        try {
            $prop = $refClass->getProperty('_'.$propertyName);
            return true;
        } catch (ReflectionException $e) {

        }

        return false;
    }

    /**
     * Will return the properties as an associative array (key/value pairs)
     * of the specified object based on the setter-methods it implements along
     * with the properties it defines. A property will only be returned if it
     * matches the definition for an <tt>Conjoon_BeanContext</tt>-property, though
     * it must not neccessarily implement <tt>Conjoon_BeanContext</tt>.
     *
     * @param Object $object An object instance of any type.
     * @return Array
     *
     * @throws Conjoon_BeanContext_Exception A call to any of the getters
     * of the passed object can throw an excepiton, which's message gets wrapped up in
     * an exception of the type Conjoon_BeanContext_Exception
     *
     * @see Conjoon_BeanContext
     */
    public static function getProperties($object)
    {
        $properties = self::getReflectionProperties($object);
        $methods    = self::getReflectionMethods($object);

        $accessorType = self::GETTER;

        $data = array();

        /**
         * @todo better regex for properties containing only letters
         */
        for ($i = 0, $len = count($properties); $i < $len; $i++) {
            $name = $properties[$i]->getName();
            if (strpos($name, '_') !== false) {
                continue;
            }

            $method = self::getAccessorForProperty($object, $name, $accessorType);
            if ($method === null) {
                continue;
            }

            try {
                $data[$name] = $object->$method();
            } catch (Exception $e) {
                throw new Conjoon_BeanContext_Exception($e->getMessage());
            }
        }

        return $data;
    }

    /**
     * Tries to instantiate a class based on the passed arguments.
     *
     * @param string $className The class that should be created. Must
     * implement the interface Conjoon_BeanContext
     * @param array $properties An associative array with key/value-pairs,
     * representing the object properties to set. The Constructor will search for
     * appropriate getter/setter methods and call them, passing the values as
     * arguments.
     * @param boolean $ignoreMissingSetter true to not throw an error if a property
     * occures for which no setter is available
     *
     * @return Object
     * @throws Conjoon_BeanContext_Exception if any error during object
     * instantiating or class loading occured
     */
    public static function create($className, $properties, $ignoreMissingSetter = false)
    {
        self::loadClass($className);

        if (!class_exists($className, false)) {
            throw new Conjoon_BeanContext_Exception("$className not found.");
        }

        $object = new $className;

        if (!($object instanceof Conjoon_BeanContext)) {
            throw new Conjoon_BeanContext_Exception("$className does not implement Conjoon_BeanContext.");
        }

        try {
            foreach ($properties as $property => $value) {
                /**
                 * @todo better regex for anything except letters
                 */
                if (self::isTransient($property, $object) === false) {
                    self::setProperty($object, $property, $value, $ignoreMissingSetter);
                }
            }
        } catch (Exception $e) {
            if (($e instanceof Conjoon_BeanContext_Exception)) {
                throw $e;
            } else {
                throw new Conjoon_BeanContext_Exception($e->getMessage() . '[originally thrown by '.get_class($e).']');
            }
        }

        return $object;
    }

    /**
     * Helper for getting a getter-method based on the property passed
     * as the argument.
     *
     * @param Object $object The object in which the accessor should be looked up
     * @param string $propertyName The name of the property which accessor has to
     * be looked up
     * @param string $accessorType Which accessor-method to look up, either 'set'
     * or 'get'
     *
     * @return mixed <tt>null</tt>, if no appropriate getter method exists
     * for the property, otherwise the method name.
     */
    private static function getAccessorForProperty($object, $propertyName, $accessorType = self::GETTER)
    {
        $prefix = 'get';

        switch ($accessorType) {
            case self::GETTER:
                $prefix = 'get';
            break;

            case self::SETTER:
                $prefix = 'set';
            break;

            default:
                return null;
        }

        $method = '';

        if ($accessorType === self::SETTER) {
            if (strpos($propertyName, 'is') === 0) {
                $method = 'set'.substr($propertyName, 2);
            } else {

                if (strpos($propertyName, 's') === strlen($propertyName)-1) {
                    $method = 'add'.ucfirst(rtrim($propertyName, 's'));
                    if (!method_exists($object, $method)) {
                        $method = 'set'.ucfirst($propertyName);
                    }
                } else {
                    $method = 'set'.ucfirst($propertyName);
                }
            }
        } else if ($accessorType === self::GETTER) {
            if (strpos($propertyName, 'is') === 0) {
                $method = 'is'.ucfirst($propertyName);
            } else {
                $method = 'get'.ucfirst($propertyName);
            }
        }

        if (method_exists($object, $method)) {
            return $method;
        }

        return null;
    }

    /**
     * Helper for setting the properties of an Conjoon_BeanContext-object.
     * Will throw an exception if the method associated with the property was
     * not found, or any exception that was thrown by the accessor-method
     * of <tt>$object</tt>.
     * The method assumes that <tt>property</tt> is already a valid property name
     * according to <tt>Conjoon_BeanContext</tt>-conventions.
     *
     * @param Object $object The instance to set the property for. Must be of type
     * <tt>Conjoon_BeanContext</tt>
     * @param string $property The property name that is about to be set.
     * @param mixed $value The value to pass as the argument to the
     * setter-method of <tt>$property</tt>
     * @param boolean $ignoreMissing when set to true, the method will not throw an error
     * if the
     *
     * @throws Exception
     * @see Conjoon_BeanContext
     */
    private static function setProperty($object, $property, $value, $ignoreMissing = false)
    {
        $method = self::getAccessorForProperty($object, $property, self::SETTER);

        if ($method === null && $ignoreMissing == false) {
            throw new Conjoon_BeanContext_Exception("Setter for $property not found.")  ;
        } else if ($method === null) {
            return;
        }

        if (is_array($value) && strpos($method, 'add') === 0) {
            $reflM = new ReflectionMethod($object, $method);
            $params = $reflM->getParameters();
            $cls = $params[0]->getClass()->getName();
            /**
             * @see Conjoon_Util_Array
             */
            require_once 'Conjoon/Util/Array.php';
            for ($i = 0, $len = count($value); $i < $len; $i++) {
                Conjoon_Util_Array::camelizeKeys($value[$i]);
                $val = Conjoon_BeanContext_Inspector::create($cls, $value[$i], true);
                $object->$method($val);
            }
        } else {
            $object->$method($value);
        }
    }

    /**
     * Helper for loading a class.
     *
     */
    public static function loadClass($className)
    {
        if (!class_exists($className, false)) {
            $path = str_replace('_', '/', $className);
            include_once $path.'.php';
        }
    }

}