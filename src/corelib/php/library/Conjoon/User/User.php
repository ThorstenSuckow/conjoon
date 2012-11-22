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

/**
 * An interface representing an user.
 *
 * @category   Conjoon
 * @package    Conjoon_User
 * @subpackage User
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

abstract class Conjoon_User_User {

    /**
     * @var string
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_firstName;

    /**
     * @var string
     */
    protected $_lastName;

    /**
     * @var string
     */
    protected $_emailAddress;

    /**
     * @var string
     */
    protected $_username;

    /**
     * Constructor.
     *
     * @param Conjoon_Modules_Default_User $options The user object from which
     * the user should be generated
     *
     * @throws Conjoon_Argument_Exception if the passed argument was not valid
     * @throws Conjoon_User_UserException if any other error occurs during
     * instantiating
     */
    abstract public function __construct($options);

    /**
     * Returns the id associated with this user.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns the first name of the user.
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->_firstName;
    }

    /**
     * Returns the last name of the user.
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->_lastName;
    }

    /**
     * Returns the email address of the user.
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->_emailAddress;
    }

    /**
     * Returns the username of the user.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

}