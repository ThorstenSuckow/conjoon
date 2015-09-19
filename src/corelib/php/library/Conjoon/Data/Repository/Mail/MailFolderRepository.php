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
 * @see \Conjoon\Data\Repository\DataRepository
 */
require_once 'Conjoon/Data/Repository/DataRepository.php';

/**
 * Interface all MailFolderRepositories have to implement.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MailFolderRepository extends \Conjoon\Data\Repository\DataRepository {

    /**
     * Returns all the direct child folders of the specified folder.
     *
     * @param \Conjoon\Data\Entity\Mail\MailFolderEntity $folder The folder for
     *        which the child folders should be returned
     *
     * @return array An array with all the child folders as data entities. Might
     *               be empty
     *
     */
    public function getChildFolders(\Conjoon\Data\Entity\Mail\MailFolderEntity $folder);


    /**
     * Checks whether the specified folder or any of its sub folders contains
     * one or more messages which are not flagged as deleted.
     * This method does consider all user-messages relationships. If there are
     * n users associated with 1 message, and x (with x <= n) users have flagged
     * the message as deleted, the method will return false. Otherwise it will
     * return true.
     *
     * @param \Conjoon\Data\Entity\Mail\MailFolderEntity $folderEntity
     *
     * @return boolean false if any message not flagged as deleted is available,
     *         otherwise true
     */
    public function hasMessages(\Conjoon\Data\Entity\Mail\MailFolderEntity $folderEntity);

}