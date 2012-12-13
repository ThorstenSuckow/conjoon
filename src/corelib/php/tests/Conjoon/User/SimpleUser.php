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
 * @see \Conjoon\User\User
 */
require_once 'Conjoon/User/User.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleUser implements \Conjoon\User\User {

    protected $userId;

    public function __construct($userId = null)
    {
    $this->userId = $userId;
    }

    public function getId(){if ($this->userId !== null)return $this->userId;}

    public function getFirstname(){}

    public function getLastname(){}

    public function getEmailAddress(){}

    public function getUserName(){}

    public function __toString()
    {
        return "" . $this->userId;
    }

}