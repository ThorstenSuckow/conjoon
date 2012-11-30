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

/**
 * A class to mediate between meail fodler services. It's purpose is to
 * represent a collection of methods that are needed by different Client
 * MailFolder services at the same time, to avoid cross references.
 *
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MailFolderCommons {

    /**
     * Creates a new instance of a folder security service.
     * A folder security service is bound to a user.
     *
     * @param array $options An array with instances of MailFolderRepository,
     *                       and a User to use.
     *                       - user: and instance of \Conjoon\User\User
     *                       - mailFolderRepository: an instance of
     *                       Conjoon\Data\Repository\Mail\MailFolderRepository
     *
     * @throws Conjoon\Argument\InvalidArgumentExcpetion
     */
    public function __construct(Array $options);

    /**
     * Returns true if the specified folder exists on client side, otherwise
     * false.
     * This method should return false whenever a client node is not found
     * in the underlying data storage, regardless if the folder exists in a
     * remote repository.
     *
     * @param MailFolder $folder
     *
     * @return boolean
     *
     * @throws ClientMailFolderServiceException
     */
    public function doesMailFolderExist(MailFolder $folder);

}