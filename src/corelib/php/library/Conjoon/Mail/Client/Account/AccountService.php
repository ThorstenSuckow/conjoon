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


namespace Conjoon\Mail\Client\Account;

/**
 * @see \Conjoon\Mail\Client\Folder\Folder
 */
require_once 'Conjoon/Mail/Client/Folder/Folder.php';


/**
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface AccountService {

    /**
     * Returns the mail account for the specified folder.
     * The folder is assumed to represent a remote mailbox
     *
     * @param \Conjoon\Mail\Client\Folder\Folder $folder
     *
     * @return null|\Conjoon\Data\Entity\Mail\MailAccountEntity
     *
     * @throws AccountServiceException
     */
    public function getMailAccountToAccessRemoteFolder(
        \Conjoon\Mail\Client\Folder\Folder $folder);

    /**
     * Returns the standard mail account for the user bound to this instance
     *
     * @return null|\Conjoon\Data\Entity\Mail\MailAccountEntity
     *
     * @throws AccountServiceException
     */
    public function getStandardMailAccount();

    /**
     * Returns the configured mail accounts for the user bound to this instance.
     * Only those accounts which are not marked as deleted are returned.
     *
     * @return array of \Conjoon\Data\Entity\Mail\MailAccountEntity. The array
     *         may be empty
     *
     * @throws AccountServiceException
     */
    public function getMailAccounts();

}