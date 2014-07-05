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


namespace Conjoon\Data\Repository;

/**
 * Interface all DataRepositories have to implement.
 *
 * @category   Conjoon_Data
 * @package    EntityManager
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface DataRepository {


    /**
     * The method marks an entity as ready for being removed from the underlying data
     * storage.
     *
     * @param  \Conjoon\Data\Entity\DataEntity
     *
     * @return boolean
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function remove(\Conjoon\Data\Entity\DataEntity $entity);

    /**
     * The method marks an entity as ready for updating, i.e. any changes
     * which were made to the entity should be written to the underlying data
     * storage once flush() gets called.
     *
     * @param  \Conjoon\Data\Entity\DataEntity
     *
     * @return \Conjoon\Data\Entity\DataEntity|null
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     *
     * @see flush()
     */
    public function register(\Conjoon\Data\Entity\DataEntity $entity);

    /**
     * Returns the entity based on the specified id.
     * Implementing classes are advised to return one and the same
     * instance for the specified id.
     *
     * @param mixed $id
     *
     * @return \Conjoon\Data\Entity\DataEntity
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function findById($id);

    /**
     * Synchronizes the registered entities with the underlying data storage.
     * Implementations should take care of rolling entities back to a previous
     * state if anything fails.
     * Registered entities will be kept available for further flush operations
     * and don't have to be re-registered.
     */
    public function flush();


    /**
     * @return string
     */
    public static function getEntityClassName();


}
