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

namespace Conjoon\Mail\Client\Security;

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
 * Folder Mock so findById/find throws InvalidArgumentException in any case.
 *
 * Class FolderMockRepository
 * @package Conjoon\Mail\Client\Account
 */
class TestFolderMockRepository extends DoctrineMailFolderRepository {

    public function __construct(){}



    public function findById($id) {
        throw new InvalidArgumentException(
            'FolderMockRepository mocks the findById');
    }

    public function find($id) {
        throw new InvalidArgumentException(
            'FolderMockRepository mocks the find');
    }

}