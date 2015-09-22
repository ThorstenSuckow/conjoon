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

namespace Conjoon\Mail\Client\Account;

use Conjoon\Data\Repository\Mail\DoctrineMailAccountRepository,
    Conjoon\Argument\InvalidArgumentException;

/**
 * @see Conjoon\Data\Repository\Mail\DoctrineMailAccountRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DoctrineMailAccountRepository.php';

/**
 * Account Mock so findById throws InvalidArgumentException in any case.
 *
 * Class AccountMockRepository
 * @package Conjoon\Mail\Client\Account
 */
class TestAccountMockRepository extends DoctrineMailAccountRepository {

    public function __construct(){}

    public function findById($id) {
        throw new InvalidArgumentException(
            'AccountMockRepository mocks the findById');
    }

}