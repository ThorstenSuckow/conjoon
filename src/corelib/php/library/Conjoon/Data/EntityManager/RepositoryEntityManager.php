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
 * @see Conjoon\Data\EntityManager\DataEntityManager
 */
require_once 'Conjoon/Data/EntityManager/DataEntityManager.php';


/**
 * Interface all mail folder entity manager have to implement.
 *
 * @category   Conjoon_Data
 * @package    EntityManager
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class RepositoryEntityManager implements DataEntityManager {

    /**
     * @var \Conjoon\Data\Repository\DataRepository
     */
    protected $_repository;

    /**
     * @param \Conjoon\Data\DataRepository
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(\Conjoon\Data\Repository\DataRepository $options)
    {

    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return null;
    }

    /**
     *@inheritdoc
     */
    public function persist(\Conjoon\Data\Entity\DataEntity $entity)
    {
        if(!($entity instanceof \Conjoon\Data\Entity\Mail\MailFolderEntity)) {
            throw new InvalidArgumentException(
                'entity must be of type '
                .'\Conjoon\Data\Entity\Mail\MailFolderEntity'
            );
        }

        return null;
    }

    /**
     *@inheritdoc
     */
    public function remove(\Conjoon\Data\Entity\DataEntity $entity)
    {
        if(!($entity instanceof \Conjoon\Data\Entity\Mail\MailFolderEntity)) {
            throw new InvalidArgumentException(
                'entity must be of type '
                    .'\Conjoon\Data\Entity\Mail\MailFolderEntity'
            );
        }

        return false;
    }

}