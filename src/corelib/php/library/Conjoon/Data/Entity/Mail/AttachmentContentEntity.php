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
 * Interface all attachment content entities have to implement.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface AttachmentContentEntity extends \Conjoon\Data\Entity\DataEntity {

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set content
     *
     * @param string $content
     * @return AttachmentContentEntity
     */
    public function setContent($content);

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();

}