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


namespace Conjoon\Mail\Client\Account;

/**
 * @see \Conjoon\Mail\Client\Folder\Folder
 */
require_once 'Conjoon/Mail/Client/Folder/Folder.php';


/**
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface AccountService {

    /**
     * Returns the mail account for the specified folder.
     * The folder is assumed to represent a remote mailbox
     *
     * @param \Conjoon\Mail\Client\Folder\Folder $folder
     *
     * @return null|\Conjoon\Data\Entity\Mail\MailAccountEntity
     *
     * @throws AccountServiceException
     */
    public function getMailAccountToAccessRemoteFolder(
        \Conjoon\Mail\Client\Folder\Folder $folder);

    /**
     * Returns the standard mail account for the user bound to this instance
     *
     * @return null|\Conjoon\Data\Entity\Mail\MailAccountEntity
     *
     * @throws AccountServiceException
     */
    public function getStandardMailAccount();

}