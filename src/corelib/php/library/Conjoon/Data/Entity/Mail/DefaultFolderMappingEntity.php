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
 * @see \Conjoon\Data\Entity\Mail\FodlerMappingEntity
 */
require_once 'Conjoon/Data/Entity/Mail/FolderMappingEntity.php';

/**
 * Default FolderMapping implementation.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFolderMappingEntity implements FolderMappingEntity {

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $globalName
     */
    private $globalName;

    /**
     * @var string $type
     */
    private $type;

    /**
     * @var Conjoon\Data\Entity\Mail\MailAccountEntity
     */
    private $mailAccount;


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setGlobalName($globalName)
    {
        $this->globalName = $globalName;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGlobalName()
    {
        return $this->globalName;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setMailAccount(
        \Conjoon\Data\Entity\Mail\MailAccountEntity $mailAccount = null)
    {
        $this->mailAccount = $mailAccount;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMailAccount()
    {
        return $this->mailAccount;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return get_class($this) . '@' . spl_object_hash($this);
    }


}
