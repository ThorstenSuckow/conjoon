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
 * @see \Conjoon\Data\Repository\DefaultDataRepository
 */
require_once 'Conjoon/Data/Repository/DefaultDataRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\MailFolderRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MailFolderRepository.php';

/**
 * The default implementation for the Doctrine MailfolderRepository.
 * Uses Zend_Db_Table for backward compatibility.
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

}