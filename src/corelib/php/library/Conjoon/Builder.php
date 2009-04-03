<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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

/**
 * This class can be used to implement a concrete strategy for returning objects
 * out of the cache or directly from the data storage.
 * Providing a cache object when instantiating an object of this class, the get() method
 * should return the object either directly out of the cache or from the data storage.
 * Later look ups should use the cache, wheras the logic to determine whether the
 * cache is still valid is done by the cache object itself.
 *
 * Example for a concrete implementation of get():
 *
 * <pre>
 *
 *  class User_Builder extends Conjoon_Builder {
 *
 *  public function get($id)
 *  {
 *      $user = null;
 *
 *      if (!$this->_cache->test($id)) {
 *
 *          $user = new User;
 *          // expensive operations here
 *
 *          $this->_cache->save($user, $id);
 *      } else {
 *          $user = $this->_cache->load($id);
 *      }
 *
 *      return $user;
 *
 *  }
 *
 * }
 *
 * </pre>
 *
 *
 * @category   Conjoon
 * @package    Conjoon
 * @subpackage Builder
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */
abstract class Conjoon_Builder {

    /**
     * @var array $_validGetOptions
     */
    protected $_validGetOptions = array();


    /**
     * @var Zend_Cache_Core $cache
     */
    protected $_cache = null;

    /**
     * Constructor.
     *
     * @param Zend_Cache_Core $cache The cache to use for retrieving or storing an
     * object
     */
    public function __construct(Zend_Cache_Core $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Checks whether the keys specified in $_validGetOptions are available in
     * $options. Throws an exception if that is not the case, otherwise calls the
     * concrete implementation of _get.
     *
     * @param array $options
     *
     * @return mixed
     *
     * @throws Conjoon_Builder_Exception if the keys specified in $_validGetOptions
     * are not specified in $options
     *
     * @see _get
     */
    public function get(Array $options)
    {
        if (!empty($this->_validGetOptions)) {
            for ($i = 0, $len = count($this->_validGetOptions); $i < $len; $i++) {
                if (!isset($options[$this->_validGetOptions[$i]])) {
                    /**
                     * @see Conjoon_Builder_Exception
                     */
                    require_once 'Conjoon/Builder/Exception.php';

                    throw new Conjoon_Builder_Exception(
                        '"'.$this->_validGetOptions[$i].'" not found in "$options"'
                    );
                }
            }
        }


        return $this->_get($options);
    }

    /**
     * An abstract function which concrete implementation has to be provided
     * in the classes deriving from Conjoon_Builder.
     * It's purpose is to return an object specified via a list of arguments.
     * The cache should be used to look up the object in the cache and return it,
     * or to model the object, store it in the cache and return it afterwards.
     * Objects returned by this instance should be serializable.
     *
     * @param array $options An associative array of options to use. If the
     * _validGetOptions property is specified, the options will be checked against
     * this property.
     *
     * @return mixed
     */
    protected abstract function _get(Array $options);


}