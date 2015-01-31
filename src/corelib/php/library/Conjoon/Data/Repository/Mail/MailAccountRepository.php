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
 * Interface all MailAccount Repositories have to implement.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MailAccountRepository extends \Conjoon\Data\Repository\DataRepository {

    /**
     * Returns the account marked as standard for the specified user.
     *
     * @param \Conjoon\User\User $user The user for whom the standard account
     *                           should be looked up
     *
     * @return null|\Conjoon\Data\Entity\Mail\MailAccountEntity
     */
    public function getStandardMailAccount(\Conjoon\User\User $user);

    /**
     * Returns all email accounts which are not marked as deleted for the
     * specified user.
     *
     * @param \Conjoon\User\User $user The user for whom the accounts should be
     *                           looked up.
     *
     * @return array of \Conjoon\Data\Entity\Mail\MailAccountEntity. The array
     *         may be empty
     */
    public function getMailAccounts(\Conjoon\User\User $user);

}