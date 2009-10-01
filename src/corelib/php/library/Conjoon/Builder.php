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
     * @var Conjoon_BeanContext_Decoratable
     */
    protected $_model = null;

    /**
     * @var array $_validGetOptions
     */
    protected $_validGetOptions = array();

    /**
     * @var array $_validTagOptions
     */
    protected $_validTagOptions = array();

    /**
     * @var Zend_Cache_Core $cache
     */
    protected $_cache = null;

    /**
     * @var string mandatory, the class the builder has to use for creating
     * cached objects
     */
    protected $_buildClass = null;

    /**
     * Constructor.
     *
     * @param Zend_Cache_Core $cache The cache to use for retrieving or storing an
     * object. If not supplied, this builder won't have any caching functionality.
     * @param Conjoon_BeanContext_Decoratable
     *
     */
    public function __construct(Zend_Cache_Core $cache = null,
        Conjoon_BeanContext_Decoratable $model = null)
    {
        $this->_cache = $cache;
        $this->_model = $model;
    }

    /**
     * @return Conjoon_BeanContext_Decoratable
     */
    public function getModel()
    {
        if (!$this->_model) {
            $this->_model = $this->_getModel();
        }

        return $this->_model;
    }

    /**
     *
     * @return Conjoon_BeanContext_Decoratable
     */
    protected abstract function _getModel();

    /**
     * Removes an object from the cache, if available.
     *
     * @param array $options An array of options which will be considered in buildId
     */
    public function remove(Array $options)
    {
        if (!$this->_cache) {
            return;
        }
        $this->_cache->remove($this->buildId($options));
    }

    /**
     * Cleans the cache based on the passed parameters.
     *
     * @param string $type Any of
     *       Zend_Cache::CLEANING_MODE_ALL
     *       Zend_Cache::CLEANING_MODE_OLD
     *       Zend_Cache::CLEANING_MODE_MATCHING_TAG
     *       Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG
     *       Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG
     * @param array $options When using any tag related clean up, this array
     * should hold all related tags for this operation
     *
     *
     * @see Zend_Cache::clean
     */
    public function clean($type, Array $options = array())
    {
        if (!$this->_cache) {
            return;
        }

        $this->_cache->clean($type, $options);
    }

    /**
     * Cleans the cache with all cached items that where tagged with
     * the tags found in the return value of getTagList.
     *
     * @see clean
     * @see getTagList
     */
    public function cleanCacheForTags(Array $options)
    {
        if (!$this->_cache) {
            return;
        }

        $tags = $this->getTagList($options);

        $this->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags);
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
        $this->_checkValidGetOptions($options);

        // prevent serialized PHP_IMCOMPLETE_CLASS
        require_once str_replace('_', '/', $this->_buildClass) . '.php';

        $cacheId = $this->buildId($options);
        $tagList = $this->getTagList($options);

        $cache = $this->_cache;

        if (!$cache) {
            return $this->_build($options, $this->getModel());
        }

        if (!($cache->test($cacheId))) {
            $data = $this->_build($options, $this->getModel());
            $cache->save($data, $cacheId, $tagList);
        } else {
            $data = $cache->load($cacheId);
        }

        return $data;
    }

    /**
     * An abstract function which concrete implementation has to be provided
     * in the classes deriving from Conjoon_Builder.
     * It's purpose is to build the objects that have to be cached.
     *
     * @param array $options
     * @param , Conjoon_BeanContext_Decoratable $model
     *
     * @return mixed
     */
    protected abstract function _build(Array $options, Conjoon_BeanContext_Decoratable $model);

    /**
     * An abstract function which returns an id that can be used to identify
     * cache objects.
     *
     * @param Array $options a hash with key/value pairs which can be used to build an
     * id
     *
     * @return String
     */
    protected abstract function _buildId(Array $options);

    /**
     * An abstract function which returns a numerical array with tag ids as their values.
     * IF this method does not return an empty array, those ids will be used to tag a cached
     * item.
     *
     * @param Array $options a hash with key/value pairs which can be used to build a
     * tag list
     *
     * @return Array
     */
    protected function _getTagList(Array $options)
    {
        return array();
    }

    /**
     * A function which returns an array with tag ids that can be used to tag a cached item.
     *
     * @param array $options An associative array of options to use. If the
     * _validTagOptions property is specified, the options will be checked against
     * this property.
     *
     * @return Array
     */
    protected function getTagList(Array $options)
    {
        $this->_checkValidTagOptions($options);
        return $this->_getTagList($options);
    }

    /**
     * A function which returns an id under which an object in the cache can be identified.
     * The returned key will be used to identify objects in the clean() and get() method of
     * this class.
     *
     * @param array $options An associative array of options to use. If the
     * _validGetOptions property is specified, the options will be checked against
     * this property.
     *
     * @return String
     */
    public function buildId(Array $options)
    {
        $this->_checkValidGetOptions($options);
        return $this->_buildId($options);
    }

    /**
     * Validates the keys of the passed array against the whitelist of keys
     * specified in $_validGetOptions.
     * Throws an exception if there is a key missing in the passed argument that was
     * specified in $_validGetOptions.
     *
     */
    protected function _checkValidGetOptions(Array $options)
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
    }

    /**
     * Validates the keys of the passed array against the whitelist of keys
     * specified in $_validTagOptions.
     * Throws an exception if there is a key missing in the passed argument that was
     * specified in $_validTagOptions.
     *
     */
    protected function _checkValidTagOptions(Array $options)
    {
        if (!empty($this->_validTagOptions)) {
            for ($i = 0, $len = count($this->_validTagOptions); $i < $len; $i++) {
                if (!isset($options[$this->_validTagOptions[$i]])) {
                    /**
                     * @see Conjoon_Builder_Exception
                     */
                    require_once 'Conjoon/Builder/Exception.php';

                    throw new Conjoon_Builder_Exception(
                        '"'.$this->_validTagOptions[$i].'" not found in "$options"'
                    );
                }
            }
        }
    }
}