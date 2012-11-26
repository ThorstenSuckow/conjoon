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
     * The method is bound to immediately send the changes to the connected
     * backend.
     *
     * @param  \Conjoon\Data\Entity\DataEntity
     *
     * @return boolean
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function remove(\Conjoon\Data\Entity\DataEntity $entity);

    /**
     * The method is bound to immediately send the changes to the connected
     * backend.
     *
     * @param  \Conjoon\Data\Entity\DataEntity
     *
     * @return \Conjoon\Data\Entity\DataEntity|null
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function persist(\Conjoon\Data\Entity\DataEntity $entity);

    /**
     * @param mixed $id
     *
     * @return \Conjoon\Data\Entity\DataEntity
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function findById($id);

    /**
     * Synchronizes the entity with the underlying data storage.
     * Implementations should take care of rolling entities back to a previous
     * state if anything fails.
     *
     */
    public function flush();


    /**
     * @return string
     */
    public static function getEntityClassName();


}