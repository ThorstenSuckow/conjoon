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
 * @see \Conjoon\Data\Repository\DataRepository
 */
require_once 'Conjoon/Data/Repository/DataRepository.php';

/**
 * @see \Conjoon\Data\Repository\RepositoryException
 */
require_once 'Conjoon/Data/Repository/RepositoryException.php';

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
 * @see Doctrine\ORM\Mapping\ClassMetadata
 */
require_once 'Doctrine/ORM/Mapping/ClassMetadata.php';

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException,
    Conjoon\Data\Repository\RepositoryException;

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
     * @var array maps all objects that where marked for removing.
     */
    protected $removeMap = array();

    /**
     * @var array
     */
    protected $flushMap = array();

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

        if (isset($this->flushMap[spl_object_hash($entity)])) {
            $this->gatherAssociations($entity, $this->flushMap, false);
            unset($this->flushMap[spl_object_hash($entity)]);
        }

        $this->removeMap[spl_object_hash($entity)] = $entity;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function register(\Conjoon\Data\Entity\DataEntity $entity)
    {
        $className = static::getEntityClassName();

        if (!($entity instanceof $className)) {
            throw new InvalidArgumentException(
                "entity must be of type $className"
            );
        }

        if (isset($this->removeMap[spl_object_hash($entity)])) {
            throw new RepositoryException(
                "entity is scheduled for deletion"
            );
        }

        $this->flushMap[spl_object_hash($entity)] = $entity;

        return $entity;
    }

    /**
     * This method helps to gather all associations of which
     * $entity is the owner to consider their changes when flushing
     * changes of the entity to the underlying datastore.
     *
     * @param Conjoon\Data\Entity\DataEntity $entity The data entity of
     *        which all associations should be gathered
     * @param array $flushMap An array of already collected associations#
     * @param boolean $include whether entities should be included or removed from
     * $flushMap. Defaults to true.
     *
     *
     */
    protected function gatherAssociations(
        \Conjoon\Data\Entity\DataEntity $entity, array &$flushMap,
        $include = true) {

        if ($include === true && isset($flushMap[spl_object_hash($entity)])) {
            return;
        }

        if ($include === true) {
            $flushMap[spl_object_hash($entity)] = $entity;
            $this->_em->persist($entity);
        } else {
            if (isset($flushMap[spl_object_hash($entity)])) {
                unset($flushMap[spl_object_hash($entity)]);
            }
        }

        $classMetaData = $this->_em->getClassMetadata(get_class($entity));

        $fields = $classMetaData->getAssociationNames();

        foreach ($fields as $name) {

            if (!$classMetaData->isAssociationInverseSide($name)) {
                continue;
            }

            $getter = 'get' . ucfirst($name);
            if (method_exists($entity, $getter)) {

                $d = $entity->$getter();
                if (is_object($d) &&
                    (($d instanceof \Doctrine\ORM\PersistentCollection) ||
                    ($d instanceof \Doctrine\Common\Collections\ArrayCollection))
                ) {
                    foreach ($d as $newV) {
                        $this->gatherAssociations($newV, $flushMap, $include);
                    }
                } else if (is_object($d)
                    && ($d instanceof \Conjoon\Data\Entity\DataEntity )) {
                    $this->gatherAssociations($d, $flushMap, $include);
                }
            }
        }
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


        $entity = $this->_em->find(static::getEntityClassName(), $data['id']);

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
        $className = static::getEntityClassName();

        // remove
        $oldEm = $this->_em;
        $em = $this->_em->create(
            $this->_em->getConnection(), $this->_em->getConfiguration()
        );
        $this->_em = $em;
        foreach ($this->removeMap as $hash => $remEnt) {

            $classMetaData = $this->_em->getClassMetadata($className);
            $idValues = $classMetaData->getIdentifierValues($remEnt);

            $query = array();

            foreach ($idValues as $name => $value) {
                $lookupId = $value;
                if (is_object($value)) {
                    $cmD = $this->_em->getClassMetadata(get_class($value));
                    $iVs = $cmD->identifier;//($value);
                    $vGetter = 'get' . ucfirst($iVs[0]);
                    $lookupId = $value->$vGetter();
                }
                $query[$name] = $lookupId;
            }

            $queryStr = array();

            foreach ($query as $name => $value) {
                $queryStr[] = 'entity.' . $name . ' = :' .$name;
            }

            $queryStr = implode(' AND ', $queryStr);

            $docQuery = $this->_em->createQuery(
                "SELECT entity FROM " .$className. " entity WHERE " . $queryStr
            );
            foreach ($query as $name => $value) {
                $docQuery->setParameter($name, $value);
            }

            $removeMe = $docQuery->getSingleResult();
            $this->_em->remove($removeMe);
            // wait for exc. if none occures, reset map!
            unset($this->removeMap[$hash]);
        }
        $this->_em->flush();
        $this->_em = $oldEm;

        $flushMap = array_values($this->flushMap);

        if (empty($flushMap)) {
            return;
        }

        $finalMap = array();
        foreach ($flushMap as $entity) {
            $this->gatherAssociations($entity, $finalMap);
        }

        $this->_em->flush($finalMap);

        $this->flushMap = array();
    }

}
