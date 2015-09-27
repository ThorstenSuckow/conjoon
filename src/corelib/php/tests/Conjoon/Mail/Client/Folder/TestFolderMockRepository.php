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

namespace Conjoon\Mail\Client\Folder;

use Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository,
    Conjoon\Argument\InvalidArgumentException;

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * @see Conjoon\Data\Repository\Mail\DoctrineMailFolderRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DoctrineMailFolderRepository.php';

/**
 * @see Conjoon\Data\Entity\Mail\DefaultMailFolderEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultMailFolderEntity.php';


/**
 * Folder Mock so register throws InvalidArgumentException in any case.
 *
 * Class FolderMockRepository
 * @package Conjoon\Mail\Client\Account
 */
class TestFolderMockRepository extends DoctrineMailFolderRepository {

    public function __construct(){}

    public function findById($id) {
        return new \Conjoon\Data\Entity\Mail\DefaultMailFolderEntity();
    }

    public function getChildFolders(\Conjoon\Data\Entity\Mail\MailFolderEntity $folder) {
        throw new InvalidArgumentException(
            'FolderMockRepository mocks the getChildFolders');
    }

    public function register(\Conjoon\Data\Entity\DataEntity $entity) {
        throw new InvalidArgumentException(
            'FolderMockRepository mocks the register');
    }

}