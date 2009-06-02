<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
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

    public function setId($id){$this->id = $id;}
    public function setAuthToken($token){$this->authToken = $token;}
    public function setPassword($password){$this->_password = $password;}
    public function setFirstname($firstname){$this->firstname = $firstname;}
    public function setLastname($lastname){$this->lastname = $lastname;}
    public function setEmailAddress($emailAddress){$this->emailAddress = $emailAddress;}
    public function setUserName($userName){$this->userName = $userName;}
    public function setLastLogin($lastLogin){$this->lastLogin = $lastLogin;}
    public function setRoot($isRoot){$this->isRoot = $isRoot;}

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
            'id'           => $this->id,
            'firstname'    => $this->firstname,
            'lastname'     => $this->lastname,
            'userName'     => $this->userName,
            'isRoot'       => $this->isRoot,
            'emailAddress' => $this->emailAddress,
            'lastLogin'    => $this->lastLogin,
            'authToken'    => $this->authToken
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