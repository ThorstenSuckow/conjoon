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


namespace Conjoon\Data\Repository\Mail;

/**
 * @see \Conjoon\Data\Repository\DoctrineDataRepository
 */
require_once 'Conjoon/Data/Repository/DoctrineDataRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\MailFolderRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MailFolderRepository.php';

/**
 * The default implementation for the Doctrine MailfolderRepository.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineMailFolderRepository
    extends \Conjoon\Data\Repository\DoctrineDataRepository
    implements MailFolderRepository {

    /**
     * @inheritdoc
     */
    public static function getEntityClassName()
    {
        return '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity';
    }

    /**
     * @inheritdoc
     */
    public function getChildFolders(\Conjoon\Data\Entity\Mail\MailFolderEntity $folder) {

        $em = $this->getEntityManager();

        $query = $em->createQuery(
            "SELECT a FROM \Conjoon\Data\Entity\Mail\DefaultMailFolderEntity a" .
            " WHERE a.parent=?1"
        );
        $query->setParameter(1, $folder->getId());

        try {
            $res = $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return array();
        }

        return $res;
    }

    /**
     * @inheritdoc
     */
    public function hasMessages(\Conjoon\Data\Entity\Mail\MailFolderEntity $folderEntity) {

        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder()
            ->select('count(message.id)')
            ->from(' \Conjoon\Data\Entity\Mail\DefaultMessageEntity', 'message')
            ->leftJoin('message.groupwareEmailFolders', 'folder')
            ->leftJoin('message.groupwareEmailItemsFlags', 'flag')
            ->where('folder = :folder')
            ->andWhere('flag.isDeleted = :isDeleted')
            ->setParameter('isDeleted', false)// find the messages
                                              // not marked as false.
                                              // if there is any found, the folder
                                              // "hasMessages"
            ->setParameter('folder', $folderEntity);

        $result =$qb->getQuery()->getSingleScalarResult();

        return $result >= 1;
    }

}