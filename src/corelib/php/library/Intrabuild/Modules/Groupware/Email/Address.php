<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Attachment.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Modules/Groupware/Email/Attachment.php $
 */

/**
 * Intrabuild_BeanContext
 */
require_once 'Intrabuild/BeanContext.php';


/**
 *
 * @uses       Intrabuild_BeanContext
 * @category   Intrabuild_Groupware
 * @package    Intrabuild_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Intrabuild_Modules_Groupware_Email_Address implements Intrabuild_BeanContext, Serializable {

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

// -------- interface Intrabuild_BeanContext

    /**
     * Returns a Dto for an instance of this class.
     *
     * @return Intrabuild_Groupware_Email_Recipient_Dto
     */
    public function getDto()
    {
        require_once 'Address/Dto.php';

        $data = $this->toArray();

        $dto = new Intrabuild_Modules_Groupware_Email_Address_Dto();
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
            return "\"" . $data['name'] . "\" <".$data['address'].">";
        }
        return $data['address'];
    }
}