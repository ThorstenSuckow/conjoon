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

namespace Conjoon\Data\Cache;

/**
 * Interface for cacheable data.
 *
 * @category   Conjoon_Cache
 * @package    Cache
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Cacheable {


    /**
     * Returns the cached instance, if available.
     *
     * @param mixed $id
     *
     * @return mixed The cached data, or null if no cached instance is available
     *
     * @throws \Conjoon\Data\Cache\CacheException
     * @throws \Conjoon\Argument\InvalidArgumentException when the wrong argument is passed.
     * implementing classes are advised to specify which kind of type for an id they allow
     */
    public function load($id);

    /**
     * Saves the data given the specified id.
     *
     * @param mixed $data
     * @param mixed $id
     * @param array $tags
     *
     * @return boolean
     *
     * @throws \Conjoon\Data\Cache\CacheException
     * @throws \Conjoon\Argument\InvalidArgumentException when the wrong argument is passed.
     * implementing classes are advised to specify which kind of type for an id they allow
     */
    public function save($data, $id, array $tags = array());

    /**
     * Removes the cached data for the specified id.
     *
     * @param mixed $id
     *
     * @return boolean
     *
     * @throws \Conjoon\Data\Cache\CacheException
     * @throws \Conjoon\Argument\InvalidArgumentException when the wrong argument is passed.
     * implementing classes are advised to specify which kind of type for an id they allow
     */
    public function remove($id);

}
