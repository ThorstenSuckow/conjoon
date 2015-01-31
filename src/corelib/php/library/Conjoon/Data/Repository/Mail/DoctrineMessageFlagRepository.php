<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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


namespace Conjoon\Data\Repository\Mail;

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException,
    Conjoon\Data\Repository\Mail\MailRepositoryException;



/**
 * @see \Conjoon\Data\Repository\DoctrineDataRepository
 */
require_once 'Conjoon/Data/Repository/DoctrineDataRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\MessageFlagRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MessageFlagRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\MailRepositoryException
 */
require_once 'Conjoon/Data/Repository/Mail/MailRepositoryException.php';

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
     * @param array $id An assoc array containing the uId and the userId
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
                'inArray'    => array('uId', 'userId')
            )
        ), $data);

        $id = $data['id'];

        ArgumentCheck::check(array(
            'uId' => array(
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


        $uId = $id['uId'];
        $userId    = $id['userId'];

        $entity = $this->find(array(
            'groupwareEmailItems' => $uId,
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

    /**
     * @inheritdoc
     */
    public function setFlagsForUser(
        \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $folderFlagCollection,
        \Conjoon\User\User $user) {

        /**
         * @refactor
         */

        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Model_Flag
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Flag.php';

        $flagModel = new \Conjoon_Modules_Groupware_Email_Item_Model_Flag();

        $userId = $user->getId();

        $flags = $folderFlagCollection->getFlagCollection()->getFlags();

        for ($i = 0, $len = count($flags); $i < $len; $i++) {
            $uId  = $flags[$i]->getUId();
            $clear      = $flags[$i]->isClear();

            switch (true) {
                case ($flags[$i]->__toString() === '\Seen'):
                        try {
                            $isRead = !$clear;
                            $flagModel->flagItemAsRead($uId, $userId, $isRead);
                        } catch (\Exception $e) {
                            throw new MailRepositoryException(
                                "Exception thrown by previous exception: "
                                . $e->getMessage(), 0, $e
                            );
                        }
                    break;

                case ($flags[$i]->__toString() === '$Junk'):
                    try {
                        $isSpam = !$clear;
                        $flagModel->flagItemAsSpam($uId, $userId, $isSpam);
                    } catch (\Exception $e) {
                        throw new MailRepositoryException(
                            "Exception thrown by previous exception: "
                                . $e->getMessage(), 0, $e
                        );
                    }
                    break;

                case ($flags[$i]->__toString() === '$NotJunk'):
                    try {
                        $flagModel->flagItemAsSpam($uId, $userId, false);
                    } catch (\Exception $e) {
                        throw new MailRepositoryException(
                            "Exception thrown by previous exception: "
                                . $e->getMessage(), 0, $e
                        );
                    }
                    break;

                default:
                    throw new MailRepositoryException(
                        "Unknown flag \"" . $flags[$i]->__toString() . "\""
                    );
                    break;


            }
        }

    }

}