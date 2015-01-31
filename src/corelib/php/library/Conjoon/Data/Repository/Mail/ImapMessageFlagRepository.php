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

/**
 * @see \Conjoon\Data\Repository\Mail\MessageFlagRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MessageFlagRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\DefaultImapRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DefaultImapRepository.php';

/**
 * A data repository connected to an imap server.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class ImapMessageFlagRepository extends DefaultImapRepository
    implements \Conjoon\Data\Repository\Mail\MessageFlagRepository {

    /**
     * @inheritdoc
     */
    public function setFlagsForUser(
            \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $folderFlagCollection,
            \Conjoon\User\User $user)
    {
        $connection = $this->getConnection(array('mailAccount' => $this->account));

        $connection->selectFolder($folderFlagCollection->getFolder());

        $flagCollection = $folderFlagCollection->getFlagCollection();

        return $connection->setFlags($flagCollection);
    }

    /**
     * @return string
     */
    public static function getEntityClassName()
    {
        return '\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity';
    }

}