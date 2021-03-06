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

/**
 * Conjoon_BeanContext
 */
require_once 'Conjoon/BeanContext.php';


/**
 * A class representing an user in the conjoon application.
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon
 * @package    Conjoon
 * @subpackage User
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */

class Conjoon_Modules_Default_User implements Conjoon_BeanContext, Serializable {

    private $id;
    private $_password;
    private $firstname;
    private $lastname;
    private $emailAddress;
    private $userName;
    private $isRoot;
    private $authToken;
    private $lastLogin;
    private $rememberMeToken;


    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getPassword(){return $this->_password;}
    public function getFirstname(){return $this->firstname;}
    public function getLastname(){return $this->lastname;}
    public function getEmailAddress(){return $this->emailAddress;}
    public function getUserName(){return $this->userName;}
    public function getAuthToken(){return $this->authToken;}
    public function getlastLogin(){return $this->lastLogin;}
    public function isRoot(){return $this->isRoot;}
    public function getRememberMeToken(){return $this->rememberMeToken;}

    public function setId($id){$this->id = $id;}
    public function setAuthToken($token){$this->authToken = $token;}
    public function setPassword($password){$this->_password = $password;}
    public function setFirstname($firstname){$this->firstname = $firstname;}
    public function setLastname($lastname){$this->lastname = $lastname;}
    public function setEmailAddress($emailAddress){$this->emailAddress = $emailAddress;}
    public function setUserName($userName){$this->userName = $userName;}
    public function setLastLogin($lastLogin){$this->lastLogin = $lastLogin;}
    public function setRoot($isRoot){$this->isRoot = $isRoot;}
    public function setRememberMeToken($rememberMeToken){$this->rememberMeToken = $rememberMeToken;}

// -------- helper
    /**
     * Returns an associative array, which key/value pairs represent
     * the properties stored by this object.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'              => $this->id,
            'firstname'       => $this->firstname,
            'lastname'        => $this->lastname,
            'userName'        => $this->userName,
            'isRoot'          => $this->isRoot,
            'emailAddress'    => $this->emailAddress,
            'lastLogin'       => $this->lastLogin,
            'authToken'       => $this->authToken,
            'rememberMeToken' => $this->rememberMeToken
        );
    }

// -------- interface Conjoon_BeanContext
    /**
     * Serializes properties and returns them as a string which can later on
     * be unserialized.
     *
     * @return string
     */
    public function serialize()
    {
        $data = $this->toArray();

        return serialize($data);
    }

    /**
     * Unserializes <tt>$serialized</tt> and assigns the specific
     * values found to the members in this class.
     *
     * @param string $serialized The serialized representation of a former
     * instance of this class.
     */
    public function unserialize($serialized)
    {
        $str = unserialize($serialized);

         foreach ($str as $member => $value) {
            $this->$member = $value;
        }
    }

    /**
     * Returns a Dto for an instance of this class.
     *
     * @return Conjoon_Groupware_Email_AccountDto
     */
    public function getDto()
    {
        require_once 'User/Dto.php';

        $data = $this->toArray();

        $dto = new Conjoon_Modules_Default_User_Dto();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }

        return $dto;
    }

    /**
     * Returns a textual representation of the current object.
     *
     * @return string
     */
    public function __toString()
    {
        $data = $this->toArray();
        return
            'id: '.$data['id'].', '.
            'firstname: '.$data['firstname'].', '.
            'lastname: '.$data['lastname'].', '.
            'userName: '.$data['userName'].', '.
            'isRoot: '.$data['isRoot'].', '.
            'authToken: '.$data['authToken'].', '.
            'lastLogin: '.$data['lastLogin'].', '.
            'emailAddress: '.$data['emailAddress'].';';
    }
}
