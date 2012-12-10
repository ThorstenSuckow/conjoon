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
 * @see \Conjoon\Data\Repository\Mail\MessageFlagRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MessageFlagRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\DefaultImapRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DefaultImapRepository.php';

/**
 * A data repository connected to an imap server.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class ImapMessageFlagRepository extends DefaultImapRepository
    implements \Conjoon\Data\Repository\Mail\MessageFlagRepository {

    /**
     * @inheritdoc
     */
    public function setFlagsForUser(
            \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $folderFlagCollection,
            \Conjoon\User\User $user)
    {
        $connection = $this->getConnection(array('mailAccount' => $this->account));

        $connection->selectFolder($folderFlagCollection->getFolder());

        $flagCollection = $folderFlagCollection->getFlagCollection();

        return $connection->setFlags($flagCollection);
    }

    /**
     * @return string
     */
    public static function getEntityClassName()
    {
        return '\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity';
    }

}