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

use Conjoon\Argument\ArgumentCheck;

use Conjoon\Argument\InvalidArgumentException;

/**
 * @see \Conjoon\Data\Repository\DataRepository
 */
require_once 'Conjoon/Data/Repository/DataRepository.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * Interface all DataRepositories have to implement.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class DefaultDataRepository implements DataRepository{

    /**
     * @inheritdoc
     */
    public function remove(\Conjoon\Data\Entity\DataEntity $entity)
    {
        $className = static::getEntityClassName();

        if (!($entity instanceof $className)) {
            throw new InvalidArgumentException(
                "entity must be of type $className"
            );
        }

        return $this->_remove($entity);
    }

    /**
     * @inheritdoc
     */
    public function persist(\Conjoon\Data\Entity\DataEntity $entity)
    {
        $className = self::getEntityClassName();

        if (!($entity instanceof $className)) {
            throw new InvalidArgumentException(
                "entity must be of type $className"
            );
        }

        return $this->_persist($entity);
    }

    /**
     * @inheritdoc
     */
    public function findById($id)
    {
        $data = array('id' => $id);

        ArgumentCheck::check(array(
            'id' => array(
                'type'        => 'int',
                'allowEmpty'  => false,
                'greaterThan' => 0
            )
        ), $data);

        $entity = $this->_findById($id);

        if ($entity === null) {
            return null;
        }

        $className = static::getEntityClassName();

        if (!($entity instanceof $className)) {
            throw new InvalidArgumentException(
                "entity must be of type $className"
            );
        }

        return $entity;
    }

}