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
