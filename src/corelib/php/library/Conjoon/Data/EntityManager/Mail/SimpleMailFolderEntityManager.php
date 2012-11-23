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

use Conjoon\Argument\ArgumentCheck;

/**
 * @see Conjoon\Data\EntityManager\Mail\MailFolderEntityManager
 */
require_once 'Conjoon/Data/EntityManager/Mail/MailFolderEntityManager.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';


/**
 * Interface all mail folder entity manager have to implement.
 *
 * @category   Conjoon_Data
 * @package    EntityManager
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleMailFolderEntityManager implements MailFolderEntityManager {

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        $data = array('id' => $id);

        ArgumentCheck::check(array(
            'id' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $data);

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