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


namespace Conjoon\Data\Repository\Mail;

use Conjoon\Argument\ArgumentCheck;

use Conjoon\Argument\InvalidArgumentException;

/**
 * @see \Conjoon\Data\Repository\DoctrineDataRepository
 */
require_once 'Conjoon/Data/Repository/DoctrineDataRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\MessageFlagRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MessageFlagRepository.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * The default implementation for the Doctrine MessageFlag Repository.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineMessageFlagRepository
    extends \Conjoon\Data\Repository\DoctrineDataRepository
    implements MessageFlagRepository {


    /**
     * Override so the composite key containing the messageId and the
     * userId can be passed.
     *
     * @param array $id An assoc array containing the messageId and the userId
     * of the flag to query.
     *
     */
    public function findById($id)
    {
        $data = array('id' => $id);

        ArgumentCheck::check(array(
            'id' => array(
                'type'       => 'array',
                'allowEmpty' => false,
                'inArray'    => array('messageId', 'userId')
            )
        ), $data);

        $id = $data['id'];

        ArgumentCheck::check(array(
            'messageId' => array(
                'type'        => 'int',
                'allowEmpty'  => false,
                'greaterThan' => 0
            ),
            'userId' => array(
                'type'        => 'int',
                'allowEmpty'  => false,
                'greaterThan' => 0
            )
        ), $id);


        $messageId = $id['messageId'];
        $userId    = $id['userId'];

        $entity = $this->find(array(
            'groupwareEmailItems' => $messageId,
            'users'               => $userId
        ));

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
    public static function getEntityClassName()
    {
        return '\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity';
    }

}