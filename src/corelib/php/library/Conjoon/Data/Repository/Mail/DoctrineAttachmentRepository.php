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


namespace Conjoon\Data\Repository\Mail;

/**
 * @see \Conjoon\Data\Repository\DoctrineDataRepository
 */
require_once 'Conjoon/Data/Repository/DoctrineDataRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\AttachmentRepository
 */
require_once 'Conjoon/Data/Repository/Mail/AttachmentRepository.php';

/**
 * The default implementation for the Doctrine AttachmentRepository.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineAttachmentRepository
    extends \Conjoon\Data\Repository\DoctrineDataRepository
    implements AttachmentRepository {

    /**
     * @inheritdoc
     */
    public static function getEntityClassName()
    {
        return '\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity';
    }

}
