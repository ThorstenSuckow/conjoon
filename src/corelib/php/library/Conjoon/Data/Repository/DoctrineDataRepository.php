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
 * @see Doctrine\ORM\EntityRepository
 */
require_once 'Doctrine/ORM/EntityRepository.php';


/**
 * A data repository based upon a doctrine repository.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class DoctrineDataRepository
    extends \Doctrine\ORM\EntityRepository
    implements DataRepository{

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

        $this->_em->remove($entity);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function persist(\Conjoon\Data\Entity\DataEntity $entity)
    {
        $className = static::getEntityClassName();

        if (!($entity instanceof $className)) {
            throw new InvalidArgumentException(
                "entity must be of type $className"
            );
        }

        $this->_em->persist($entity);

        return $entity;
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

        $entity = $this->find($id);

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

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->_em->transactional(function(){});
    }

}