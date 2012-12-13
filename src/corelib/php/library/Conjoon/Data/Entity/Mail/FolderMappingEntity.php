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