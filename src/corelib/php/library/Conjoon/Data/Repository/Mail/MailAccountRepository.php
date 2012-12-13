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
 * Interface all MailAccount Repositories have to implement.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MailAccountRepository extends \Conjoon\Data\Repository\DataRepository {

    /**
     * returns the account marked as standard for the specified user.
     *
     * @param \Conjoon\User\User $user The user for whom the standard account
     *                           should be looked up
     *
     * @return null|\Conjoon\Data\Entity\Mail\MailAccountEntity
     */
    public function getStandardMailAccount(\Conjoon\User\User $user);

}