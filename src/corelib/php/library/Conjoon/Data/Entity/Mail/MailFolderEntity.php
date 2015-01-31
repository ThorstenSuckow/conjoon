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
 * Interface all MailFolderEntity entities have to implement.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MailFolderEntity extends \Conjoon\Data\Entity\DataEntity {


    public function getId();
    public function getName();
    public function getIsChildAllowed();
    public function getIsLocked();
    public function getType();
    public function getMetaInfo();
    public function getIsDeleted();

    /**
     * Conjoon_Data_Entity_Mail_MailFolderEntity
     *
     * @return
     */
    public function getParent();


    public function setId($id);
    public function setName($name);
    public function setIsChildAllowed($isChildAllowed);
    public function setIsLocked($isLocked);
    public function setType($type);
    public function setMetaInfo($metaInfo);
    public function setIsDeleted($isDeleted);

    /**
     *
     *
     * @return
     */
    public function setParent(MailFolderEntity $parent = null);

    /**
     * Get mailccount
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMailAccounts();

}