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
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

use Conjoon\Argument\ArgumentCheck;

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

    /**
     * Returns an entity identified by the passed $id. The $id
     * in this case must be of type \Conjoon\Mail\Client\Message\AttachmentLocation
     * representing the location of the attachment OR the raw id of the data
     * as managed by the underlying data storage.
     *
     * @param {mixed} $id
     *
     * @return \Conjoon\Data\Entity\DataEntity
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function findById($id) {

        if (is_object($id)) {
            $data = array('attachmentLocation' => $id);

            ArgumentCheck::check(array(
                'attachmentLocation' => array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Mail\Client\Message\AttachmentLocation'
                )
            ), $data);

            $attachmentLocation = $data['attachmentLocation'];

            $key = $attachmentLocation->getIdentifier();

            $messageLocation = $attachmentLocation->getMessageLocation();
            $messageUid = $messageLocation->getUid();

            $query = $this->getEntityManager()->createQuery(
                "SELECT a from Conjoon\Data\Entity\Mail\DefaultAttachmentEntity a " .
                "WHERE a.key = :key and a.message = :messageUid"
            );
            $query->setParameter('key', $key);
            $query->setParameter('messageUid', $messageUid);

            try {
                return $query->getSingleResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }

        }

        return parent::findById($id);
    }

}
