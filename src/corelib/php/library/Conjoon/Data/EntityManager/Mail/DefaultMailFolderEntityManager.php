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

namespace Conjoon\Data\EntityManager\Mail;

use Conjoon\Argument\InvalidArgumentException;

use Conjoon\Data\Entitymanager\RepositoryEntityManager;

/**
 * @see Conjoon\Data\EntityManager\RepositoryEntityManager
 */
require_once 'Conjoon/Data/EntityManager/RepositoryEntityManager.php';

/**
 * @see Conjoon\Data\EntityManager\Mail\MailFolderEntityManager
 */
require_once 'Conjoon/Data/EntityManager/Mail/MailFolderEntityManager.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * Interface all mail folder entity manager have to implement.
 *
 * @category   Conjoon_Data
 * @package    EntityManager
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailFolderEntityManager
    extends RepositoryEntityManager implements MailFolderEntityManager {

    /**
     * @param $options = null
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct($options)
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