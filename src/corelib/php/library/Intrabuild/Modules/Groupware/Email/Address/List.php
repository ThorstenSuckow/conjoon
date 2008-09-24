<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Message.php 68 2008-08-02 13:12:03Z T. Suckow $
 * $Date: 2008-08-02 15:12:03 +0200 (Sa, 02 Aug 2008) $
 * $Revision: 68 $
 * $LastChangedDate: 2008-08-02 15:12:03 +0200 (Sa, 02 Aug 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Modules/Groupware/Email/Message.php $
 */

/**
 * Intrabuild_BeanContext
 */
require_once 'Intrabuild/BeanContext.php';


/**
 * A collection of Intrabuild_Modules_Groupware_Email_Address entries
 *
 * @uses       Intrabuild_BeanContext
 * @category   Intrabuild_Groupware
 * @package    Intrabuild_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Intrabuild_Modules_Groupware_Email_Address_List implements Intrabuild_BeanContext, Serializable {

    private $addresses;

    /**
     * Constructor.
     */
    public function __construct(Array $list = array())
    {
        $this->addresses = $list;
    }

// -------- accessors

    public function getAddresses(){return $this->addresses;}

    public function setAddresses(Array $addresses){$this->addresses = $addresses;}


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
     * @return Intrabuild_Groupware_Email_AccountDto
     */
    public function getDto()
    {
        require_once 'List/Dto.php';

        $data = $this->toArray();

        $dto = new Intrabuild_Modules_Groupware_Email_Address_List_Dto();
        foreach ($data as $key => $value) {
            if ($key == 'addresses') {
                $addr = array();
                foreach ($this->addresses as $address) {
                    $addr = $address->getDto();
                }
                $dto->$key = $addr;
            }
            $dto->$key = $value;
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
        $addresses = array();
        for ($i = 0; $i < count($this->addresses); $i++) {
            $addresses[] = $this->addresses[$i]->toArray();
        }

        return array(
            'addresses' => $addresses
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

        $strs = array();
        foreach ($data as $key => $value) {
            if ($key == 'addresses') {
                $addresses = array();
                for ($i = 0; $i < count($this->addresses); $i++) {
                    $addresses[] = $this->addresses[$i]->__toString();
                }
                $strs[] = 'addresses: ['.implode(';', $addresses).']';
            } else {
                $strs[] = $key.': '.$value;
            }
        }
        return get_class($this).'['.implode('; ', $strs).']';
    }
}