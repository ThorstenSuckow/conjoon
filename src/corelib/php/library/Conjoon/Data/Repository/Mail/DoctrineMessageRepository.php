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
