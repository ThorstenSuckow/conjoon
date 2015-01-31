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
