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

namespace Conjoon\Data\EntityManager;

/**
 * Interface all data entity manager have to implement.
 *
 * @category   Conjoon_Data
 * @package    EntityManager
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface DataEntityManager {


    /**
     * @param $id
     *
     * @retun \Conjoon\Data\Entity\DataEntity|null
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function find($id);

    /**
     *
     * @param \Conjoon\Data\Entity\DataEntity $entity
     *
     * @return mixed|null
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function persist(\Conjoon\Data\Entity\DataEntity $entity);

    /**
     *
     * @param \Conjoon\Data\Entity\DataEntity $entity
     *
     * @return bool
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function remove(\Conjoon\Data\Entity\DataEntity $entity);

}