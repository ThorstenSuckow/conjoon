<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
 * licensing@conjoon.org
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
