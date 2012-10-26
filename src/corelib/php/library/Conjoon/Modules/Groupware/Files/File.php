<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * @see Conjoon_Modules_Groupware_Files_File_Dto
 */
require_once 'Conjoon/Modules/Groupware/Files/File/Dto.php';


/**
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon_Groupware
 * @package    Conjoon_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Conjoon_Modules_Groupware_Files_File implements Conjoon_BeanContext, Serializable {

    private $id;
    private $groupwareFilesFoldersId;
    private $key;
    private $name;
    private $mimeType;
    private $metaType;

    /**
     * Constructor.
     */
    public function __construct()
    {

    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getName(){return $this->name;}
    public function getMimeType(){return $this->mimeType;}
    public function getMetaType(){return $this->metaType;}
    public function getKey(){return $this->key;}
    public function getGroupwareFilesFoldersId(){return $this->groupwareFilesFoldersId;}

    public function setId($id){$this->id = $id;}
    public function setName($name){$this->name = $name;}
    public function setMimeType($mimeType){$this->mimeType = $mimeType;}
    public function setMetaType($metaType){$this->metaType = $metaType;}
    public function setKey($key){$this->key = $key;}
    public function setGroupwareFilesFoldersId($groupwareFilesFoldersId){$this->groupwareFilesFoldersId = $groupwareFilesFoldersId;}


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
     * @return Conjoon_Groupware_Files_File_Dto
     */
    public function getDto()
    {
        $data = $this->toArray();

        $dto = new Conjoon_Modules_Groupware_Files_File_Dto();
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
            'id'                      => $this->id,
            'name'                    => $this->name,
            'mimeType'                => $this->mimeType,
            'key'                     => $this->key,
            'metaType'                => $this->metaType,
            'groupwareFilesFoldersId' => $this->groupwareFilesFoldersId
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
            $strs[] = $key.': '.$value;
        }
        return get_class($this).'['.implode('; ', $strs).']';
    }
}