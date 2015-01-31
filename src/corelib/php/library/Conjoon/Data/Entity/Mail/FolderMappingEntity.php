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


namespace Conjoon\Data\Entity\Mail;

/**
 * @see \Conjoon\Data\Entity\DataEntity
 */
require_once 'Conjoon/Data/Entity/DataEntity.php';

/**
 * Interface all FolderMapping entities have to implement.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface FolderMappingEntity extends \Conjoon\Data\Entity\DataEntity {

    /**
     * Set globalName
     *
     * @param string $globalName
     * @return DefaultFolderMappingEntity
     */
    public function setGlobalName($globalName);

    /**
     * Get globalName
     *
     * @return string
     */
    public function getGlobalName();

    /**
     * Set type
     *
     * @param string $type
     * @return DefaultFolderMappingEntity
     */
    public function setType($type);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set mailAccount
     *
     * @param Conjoon\Data\Entity\Mail\MailAccountEntity $mailAccount
     * @return DefaultFolderMappingEntity
     */
    public function setMailAccount(
        \Conjoon\Data\Entity\Mail\MailAccountEntity $mailAccount = null);

    /**
     * Get mailAccount
     *
     * @return Conjoon\Data\Entity\Mail\DefaultMailAccountEntity
     */
    public function getMailAccount();

}