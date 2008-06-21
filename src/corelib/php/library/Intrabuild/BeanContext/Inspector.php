<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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
 * Intrabuild_BeanContext
 */ 
require_once 'Intrabuild/BeanContext.php';

/**
 * Intrabuild_BeanContext_Exception
 */ 
require_once 'Intrabuild/BeanContext/Exception.php';
 
/**
 * Static class with utility methods for working on classes implementing the 
 * Intrabuild_BeanContext-interface and overall introspection of classes/object-instances.
 *
 * @category   Intrabuild
 * @package    Intrabuild_BeanContext
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */ 
class Intrabuild_BeanContext_Inspector {
    
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
     * matches the definition for an <tt>Intrabuild_BeanContext</tt>-property, though
     * it must not neccessarily implement <tt>Intrabuild_BeanContext</tt>.
     *
     * @param Object $object An object instance of any type.
     * @return Array
     *
     * @throws Intrabuild_BeanContext_Exception A call to any of the getters
     * of the passed object can throw an excepiton, which's message gets wrapped up in
     * an exception of the type Intrabuild_BeanContext_Exception
     *
     * @see Intrabuild_BeanContext
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
                throw new Intrabuild_BeanContext_Exception($e->getMessage());    
            }
        } 
        
        return $data;
    }

    /**
     * Tries to instantiate a class based on the passed arguments.
     * 
     * @param string $className The class that should be created. Must
     * implement the interface Intrabuild_BeanContext
     * @param array $properties An associative array with key/value-pairs,
     * representing the object properties to set. The Constructor will search for
     * appropriate getter/setter methods and call them, passing the values as 
     * arguments.
     * 
     * @return Object
     * @throws Intrabuild_BeanContext_Exception if any error during object 
     * instantiating or class loading occured
     */
    public static function create($className, $properties)
    {
        self::loadClass($className);
        
        if (!class_exists($className, false)) {
            throw new Intrabuild_BeanContext_Exception("$className not found.");    
        } 
          
        $object = new $className;    
        
        if (!($object instanceof Intrabuild_BeanContext)) {
            throw new Intrabuild_BeanContext_Exception("$className does not implement Intrabuild_BeanContext.");        
        }
        
        try {
            foreach ($properties as $property => $value) {
                /**
                 * @todo better regex for anything except letters
                 */
                if (self::isTransient($property, $object) === false) {
                    self::setProperty($object, $property, $value);    
                }
            }
        } catch (Exception $e) {
            if (($e instanceof Intrabuild_BeanContext_Exception)) {
                throw $e;    
            } else {
                throw new Intrabuild_BeanContext_Exception($e->getMessage() . '[originally thrown by '.get_class($e).']');    
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
                $method = 'set'.ucfirst($propertyName);
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
     * Helper for setting the properties of an Intrabuild_BeanContext-object.
     * Will throw an exception if the method associated with the property was
     * not found, or any exception that was thrown by the accessor-method
     * of <tt>$object</tt>.
     * The method assumes that <tt>property</tt> is already a valid property name
     * according to <tt>Intrabuild_BeanContext</tt>-conventions.
     *
     * @param Object $object The instance to set the property for. Must be of type
     * <tt>Intrabuild_BeanContext</tt>
     * @param string $property The property name that is about to be set.
     * @param mixed $value The value to pass as the argument to the 
     * setter-method of <tt>$property</tt>
     *
     * @throws Exception 
     * @see Intrabuild_BeanContext
     */
    private static function setProperty($object, $property, $value)
    {
        $method = self::getAccessorForProperty($object, $property, self::SETTER);
        
        if ($method === null) {
            throw new Intrabuild_BeanContext_Exception("Setter for $property not found.")  ;
        }
        
        $object->$method($value);    
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