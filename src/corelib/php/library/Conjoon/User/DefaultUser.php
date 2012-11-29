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

require_once 'Conjoon/User/User.php';

/**
 * An abstract class for a default user implementation.
 *
 * @category   Conjoon
 * @package    Conjoon_User
 * @subpackage User
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

abstract class DefaultUser implements User {

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
    protected $_userName;

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
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @inheritdoc
     */
    public function getFirstname()
    {
        return $this->_firstName;
    }

    /**
     * @inheritdoc
     */
    public function getLastname()
    {
        return $this->_lastName;
    }

    /**
     * @inheritdoc
     */
    public function getEmailAddress()
    {
        return $this->_emailAddress;
    }

    /**
     * @inheritdoc
     */
    public function getUserName()
    {
        return $this->_userName;
    }

}