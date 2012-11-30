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

namespace Conjoon\Mail\Client\Security;



/**
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MailFolderSecurityService {

    /**
     * Creates a new instance of a folder security service.
     * A folder security service is bound to a user.
     *
     * @param array $options An array with instances of MailFolderRepository,
     *                       a User and the FolderService to use.
     *                       - user: and instance of \Conjoon\User\User
     *                       - mailFolderRepository: an instance of
     *                       Conjoon\Data\Repository\Mail\MailFolderRepository
     *                       - mailFolderCommons an instance of
     *                       Conjoon\Mail\Client\Folder\FolderCommons
     *
     * @throws Conjoon\Argument\InvalidArgumentExcpetion
     */
    public function __construct(Array $options);

    /**
     * Checks whether the user bound to this service may access the specified
     * folder.
     * Access means read and write permissions.
     * The implementing classes should consider the possibility that the sent
     * folder represents a remote folder which's path exists only on the remote
     * server, but is not mapped on the client site yet.
     * The method should then first check the root id, and if this one is
     * accessible by the current user, the path should be checked for existance
     * on the client side. If the path does only exist on remote site, it should
     * be assumed that the path is accessible since the root id is accessible.
     * Another case is the one that comes with empty paths, i.e. where only the
     * root id is available.
     * If a path is available, and this path exists on client side, only the
     * last path part of this path should be checked for accessibility.
     *
     * @param \Conjoon\Mail\Client\Folder\MailFolder $folder The folder to check
     *
     * @return boolean true on success, false if access is forbidden
     *
     * @throws SecurityServiceException
     */
    public function isMailFolderAccessible(
        \Conjoon\Mail\Client\Folder\Folder $folder);


}