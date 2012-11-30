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

namespace Conjoon\Mail\Client\Folder;

use Conjoon\Data\Repository\Mail\MailFolderRepository,
    Conjoon\User\User;

/**
 * @see \Conjoon\Mail\Client\Folder\MailFolder
 */
require_once 'Conjoon/Mail/Client/Folder/MailFolder.php';


/**
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ClientMailFolderService {

    /**
     * Creates a new instance of a folder service.
     * A folder service is bound to a user.
     *
     * @param array $options An array with instances of MailFolderRepository,
     *                       and a User to use.
     *                       - user: and instance of \Conjoon\User\User
     *                       - mailFolderRepository: an instance of
     *                       Conjoon\Data\Repository\Mail\MailFolderRepository
     *                       - mailFolderCommons: an instance of
     *                       Conjoon\Mail\Client\Folder\MailFolderCommons
     *
     */
    public function __construct(Array $options);

    /**
     * Returns true if the specified folder represents a remote folder,
     * otherwise false.
     *
     * @param MailFolder $folder
     *
     * @return boolean
     *
     * @throws ClientMailFolderServiceException
     */
    public function isClientMailFolderRepresentingRemoteMailbox(MailFolder $folder);

}