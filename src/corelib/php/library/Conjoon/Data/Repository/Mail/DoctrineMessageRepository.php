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

/**
 * @see \Conjoon\Data\Repository\DoctrineDataRepository
 */
require_once 'Conjoon/Data/Repository/DoctrineDataRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\MessageRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MessageRepository.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException;

/**
 * The default implementation for the Doctrine Nessage Repository.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineMessageRepository
    extends \Conjoon\Data\Repository\DoctrineDataRepository
    implements MessageRepository {

    /**
     * @inheritdoc
     */
    public static function getEntityClassName()
    {
        return '\Conjoon\Data\Entity\Mail\DefaultMessageEntity';
    }


    /**
     * Returns an entity identified by the passed $id. The $id
     * in this case must be of type \Conjoon\Mail\Client\Message\MessageLocation
     * representing the location of the message or the raw id of the data
     * as managed by teh udnerlying data storage..
     *
     * @param \Conjoon\Mail\Client\Message\MessageLocation $id
     *
     * @return \Conjoon\Data\Entity\DataEntity
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function findById($id) {

        if (is_object($id)) {
            $data = array('messageLocation' => $id);

            ArgumentCheck::check(array(
                'messageLocation' => array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Mail\Client\Message\MessageLocation'
                )
            ), $data);

            $id = $id->getUId();
        }

        return parent::findById($id);
    }
}
