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
 * Intrabuild_BeanContext
 */
require_once 'Intrabuild/BeanContext.php';


/**
 * A class representing an user in the intrabuild application.
 *
 * @uses       Intrabuild_BeanContext
 * @category   Intrabuild
 * @package    Intrabuild
 * @subpackage User
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Intrabuild_Modules_Default_User implements Intrabuild_BeanContext, Serializable {

    private $id;
    private $_password;
    private $firstname;
    private $lastname;
    private $emailAddress;


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

    public function setId($id){$this->id = $id;}
    public function setPassword($password){$this->_password = $password;}
    public function setFirstname($firstname){$this->firstname = $firstname;}
    public function setLastname($lastname){$this->lastname = $lastname;}
    public function setEmailAddress($emailAddress){$this->emailAddress = $emailAddress;}

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
            'emailAddress' => $this->emailAddress
        );
    }

// -------- interface Intrabuild_BeanContext
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
     * @return Intrabuild_Groupware_Email_AccountDto
     */
    public function getDto()
    {
        require_once 'User/Dto.php';

        $data = $this->toArray();

        $dto = new Intrabuild_Modules_Default_User_Dto();
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
            'emailAddress: '.$data['emailAddress'].';';
    }
}