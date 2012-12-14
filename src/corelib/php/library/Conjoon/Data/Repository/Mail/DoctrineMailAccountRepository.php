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
            . ",\Conjoon\Data\Entity\User\DefaultUserEntity u"
            . " WHERE u.id = ?1 "
            . " AND a.user = u "
            . " AND a.isStandard = ?2 "
            . " AND a.isDeleted = ?3"
        );
        $query->setParameter(1, $user->getId());
        $query->setParameter(2, true);
        $query->setParameter(3, false);

        try {
            $res = $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

        return $res;
    }

    /**
     * @inheritdoc
     */
    public function getMailAccounts(\Conjoon\User\User $user)
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            "SELECT a FROM \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity a"
             . ",\Conjoon\Data\Entity\User\DefaultUserEntity u"
                . " WHERE u.id = ?1 "
                . " AND a.user=u "
                . " AND a.isDeleted = ?2"
        );
        $query->setParameter(1, $user->getId());
        $query->setParameter(2, false);

        try {
            $res = $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return array();
        }

        return $res;
    }

}