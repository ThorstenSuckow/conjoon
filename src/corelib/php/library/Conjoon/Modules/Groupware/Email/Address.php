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
 * @see Conjoon_BeanContext
 */
require_once 'Conjoon/BeanContext.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Address_Dto
 */
require_once 'Conjoon/Modules/Groupware/Email/Address/Dto.php';


/**
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon_Groupware
 * @package    Conjoon_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */

class Conjoon_Modules_Groupware_Email_Address implements Conjoon_BeanContext, Serializable {

    private $name;
    private $address;


    /**
     * Constructor.
     *
     * @param Array $parts A numeric array whereas the first index is the email address, and
     * the second is the name
     */
    public function __construct(Array $parts = array())
    {
        if (!empty($parts)) {
            $this->address = $parts[0];
            if (isset($parts[1])) {
                if ($parts[1] != $parts[0]) {
                    $this->name = $parts[1];
                }
            }
        }
    }


// -------- accessors

    public function getName(){return $this->name;}
    public function getAddress(){return $this->address;}

    public function setName($name){$this->name = $name;}
    public function setAddress($address){$this->address = $address;}


// -------- interface Serializable
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

// -------- interface Conjoon_BeanContext

    /**
     * Returns a Dto for an instance of this class.
     *
     * @return Conjoon_Modules_Groupware_Email_Address_Dto
     */
    public function getDto()
    {
        $data = $this->toArray();

        $dto = new Conjoon_Modules_Groupware_Email_Address_Dto();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                    $dto->$key = $value;
            }
        }

        return $dto;
    }

    /**
     * Returns an associative array, which key/value pairs represent
     * the properties stored by this object.
     *
     * @return array
     */
    public function toArray()
    {

        return array(
            'name'    => $this->name,
            'address' => $this->address
        );
    }

    /**
     * Returns a textual representation of the current object.
     *
     * @return string
     */
    public function __toString()
    {
        $data = $this->toArray();

        if ($data['name']) {
            return $data['name'] . "  <".$data['address'].">";
        }
        return $data['address'];
    }
}