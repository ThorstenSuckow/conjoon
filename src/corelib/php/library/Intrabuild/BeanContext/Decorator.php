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
 * @see Intrabuild_BeanContext_Inspector
 */
require_once 'Intrabuild/BeanContext/Inspector.php';

/**
 * @see Intrabuild_Util_Array
 */
require_once 'Intrabuild/Util/Array.php';

/**
 * @see Intrabuild_Filter_Input
 */
require_once 'Intrabuild/Filter/Input.php';

/**
 * A decorator for casting return values of specified model-methods
 * to their applicable objects. If no susbstiute for the specified
 * method to decorate can be found, the method/property of the decorated
 * class will be used.
 *
 * A ModelDecorator casts abstract storage data to the entities they represent.
 *
 * @package Intrabuild_BeanContext
 * @subpackage BeanContext
 * @category BeanContext
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
final class Intrabuild_BeanContext_Decorator {

    const TYPE_DTO     = 'dto';
    const TYPE_ENTITY  = 'entity';
    const TYPE_ARRAY   = 'array';
    const TYPE_DEFAULT = 'default';

    /**
     * @var Intrabuild_Filter_Input
     */
    protected $_filter = null;

    /**
     * @var string
     */
    protected $_entity = null;

    /**
     * @var array
     */
    protected $_decoratedMethods = array();

    /**
     * @var Zend_Db_Table
     */
    protected $_decoratedModel = null;

    /**
     * @var boolean
     */
    protected $_strict = true;

    /**
     * Constructor
     *
     * @param Intrabuild_BeanContext_Decoratable $mixed  The table class to model, or the classname
     * as a string of the model to decorate. The class will then try to
     * load the class and instantiate it with no arguments.
     * @param Intrabuild_Filter_Input $filter An optional filter that gets processed before the
     * data is returned. The flter will only be used if teh data is decorated as dto or entity
     * @param boolean $strict if set to false, mising setters for any property available will not
     * throw an exception
     *
     * @throws Exception
     */
    final public function __construct($mixed, Intrabuild_Filter_Input $filter = null, $strict = true)
    {
        $this->_filter = $filter;

        $this->_strict = $strict;

        if (is_string($mixed)) {
            Intrabuild_BeanContext_Inspector::loadClass($mixed);
            if (!class_exists($mixed)) {
                throw new Exception("Could not find class to decorate \"$mixed\".");
            }
            $this->_decoratedModel = new $mixed;
        } else {
            $this->_decoratedModel = $mixed;
        }

        if (!is_a($this->_decoratedModel, 'Intrabuild_BeanContext_Decoratable')) {
            throw new Exception("Decorated class is no subclass of \"Intrabuild_BeanContext_Decoratable\"");
        }

        $this->_decoratedMethods = $this->_decoratedModel->getDecoratableMethods();

        $this->_entity = $this->_decoratedModel->getRepresentedEntity();

        Intrabuild_BeanContext_Inspector::loadClass($this->_entity);
        if (!class_exists($this->_entity)) {
            throw new Exception("Could not find entity \"".$this->_entity."\".");
        }

        $refC = new ReflectionClass($this->_entity);
        if (!$refC->implementsInterface('Intrabuild_BeanContext')) {
            throw new Exception("Entity \"".$this->_entity."\" is not a \"Intrabuild_BeanContext\"-class.");
        }
    }

    /**
     * Method tries to determine if the called method was defined in
     * $_decoratedMethods. If this is the case, the return values will
     * be casted to instances of teh class defined in $_entity.
     * If the method called was not found in $_decorated, the method
     * will be called on the object this class decorates.
     *
     * @param string $method The method name called
     * @param array $arguments The arguments passed.
     *
     * @return mixed
     */
    final public function __call($method, $arguments)
    {
        $type = self::TYPE_DEFAULT;
        // check if the user wants to fetch the data as Dto
        if (strpos(strrev($method), strrev('AsDto')) === 0) {
            $type = self::TYPE_DTO;
            $method = strrev(substr(strrev($method), 5));
        }
        // check if the user wants to fetch the data as Entity
        else if (strpos(strrev($method), strrev('AsEntity')) === 0) {
            $type = self::TYPE_ENTITY;
            $method = strrev(substr(strrev($method), 8));
        }
        // check if the user wants to fetch the data as plain array
        else if (strpos(strrev($method), strrev('AsArray')) === 0) {
            $type = self::TYPE_ARRAY;
            $method = strrev(substr(strrev($method), 7));
        }

        if (in_array($method, $this->_decoratedMethods) && $type !== self::TYPE_DEFAULT) {
            $val = call_user_func_array(array($this->_decoratedModel, $method), $arguments);
            return $this->_cast($val, $type);
        } else {
            return call_user_func_array(array($this->_decoratedModel, $method), $arguments);
        }
    }

    /**
     * Tries to cast a numeric/associative array into it's representant
     * entity model as defined in $_entity.
     *
     * @param array $data The array data, if numeric, a numeric array
     * will be returned, with values of the type $className. If an
     * associative array was submitted, a single object of the type
     * $className will be returned.
     *
     * @return mixed Either an array of objects or a single object.
     */
    final protected function _cast($values, $type)
    {
        if (is_a($values, 'Zend_Db_Table_Rowset_Abstract') ||
            is_a($values, 'Zend_Db_Table_Row_Abstract') ||
            (is_object($values) && method_exists($values, 'toArray'))) {
            $values = $values->toArray();
        }

        if (!is_array($values) || $type == self::TYPE_ARRAY) {
            return $values;
        }

        $len = count($values);
        if ($len == 0) {
            return array();
        }

        $entity = $this->_entity;

        // simple check to determine if we have a assoc or numeric
        // array
        if (!Intrabuild_Util_Array::isAssociative($values)) {
            $data = array();
            for ($i = 0; $i < $len; $i++) {
                Intrabuild_Util_Array::camelizeKeys($values[$i]);

                if ($this->_filter) {
                    $values[$i] = $this->_filter
                                       ->setData($values[$i])
                                       ->getProcessedData();
                }

                $data[] = Intrabuild_BeanContext_Inspector::create(
                    $entity,
                    $values[$i],
                    !$this->_strict
                );
                if ($type == self::TYPE_DTO) {
                    $data[$i] = $data[$i]->getDto();
                }
            }
            return $data;
        } else {
            Intrabuild_Util_Array::camelizeKeys($values);
            if ($this->_filter) {
                    $values = $this->_filter
                                   ->setData($values)
                                   ->getProcessedData();
            }
            $data = Intrabuild_BeanContext_Inspector::create($entity, $values, !$this->_strict);
            if ($type == self::TYPE_DTO) {
                return $data->getDto();
            }
            return $data;
        }
    }


}