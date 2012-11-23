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

}