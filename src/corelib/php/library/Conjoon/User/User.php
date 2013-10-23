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

namespace Conjoon\User;

/**
 * An interface representing an user.
 *
 * @category   Conjoon
 * @package    Conjoon_User
 * @subpackage User
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

interface User {

    /**
     * Returns the id associated with this user.
     *
     * @return string
     */
    public function getId();

    /**
     * Returns the first name of the user.
     *
     * @return string
     */
    public function getFirstname();

    /**
     * Returns the last name of the user.
     *
     * @return string
     */
    public function getLastname();

    /**
     * Returns the email address of the user.
     *
     * @return string
     */
    public function getEmailAddress();

    /**
     * Returns the username of the user.
     *
     * @return string
     */
    public function getUserName();

}
