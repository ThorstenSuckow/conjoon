<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return
            str_replace(
                array("{id}", "{firstname}", "{lastname}",
                      "{emailAddress}", "{userName}"),
                array($this->getId(), $this->getFirstname(),
                    $this->getLastname(), $this->getEmailAddress(),
                    $this->getUserName()
                ),
                "id:{id};firstname:{firstname};lastname:{lastname};"
                . "emailAddess:{emailAddress};userName:{userName}]"
            );

    }

    /**
     * @inheritdoc
     */
    public function equals($obj) {

        if (is_object($obj) && ($obj instanceof \Conjoon\User\User)) {

            if ((string)$obj->getId() === (string)$this->getId()) {
                return true;
            }

        }

        return false;
    }
}