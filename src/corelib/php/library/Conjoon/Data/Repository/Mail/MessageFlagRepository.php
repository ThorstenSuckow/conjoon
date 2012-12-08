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
 * @see \Conjoon\Data\Repository\DataRepository
 */
require_once 'Conjoon/Data/Repository/DataRepository.php';

/**
 * Interface all MessageFlagRepositories have to implement.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MessageFlagRepository extends \Conjoon\Data\Repository\DataRepository {


    /**
     * Applies the message flag to the messages for the specified user.
     *
     * @param \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $folderFlagCollection
     * @param \Conjoon\User\User $user
     *
     * @return boolean true if the operation succeeded, otherwise false
     *
     * @throws \Conjoon\Data\Repository\Mail\MailRepositoryException
     */
    public function setFlagsForUser(
            \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $folderFlagCollection,
            \Conjoon\User\User $user);


}