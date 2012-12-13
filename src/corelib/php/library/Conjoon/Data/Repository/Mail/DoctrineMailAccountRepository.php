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
 * @see \Conjoon\Data\Repository\Mail\MailAccountRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MailAccountRepository.php';

/**
 * The default implementation for the Doctrine MailAccountRepository.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineMailAccountRepository
    extends \Conjoon\Data\Repository\DoctrineDataRepository
    implements MailAccountRepository {

    /**
     * @inheritdoc
     */
    public static function getEntityClassName()
    {
        return '\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity';
    }

    /**
     * @inheritdoc
     */
    public function getStandardMailAccount(\Conjoon\User\User $user)
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            "SELECT a FROM \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity a "
            . " WHERE a.user = ?1 "
            . " AND a.isStandard = ?2 "
        );
        $query->setParameter(1, $user);
        $query->setParameter(2, true);

        try {
            $res = $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

        return $res;
    }

}