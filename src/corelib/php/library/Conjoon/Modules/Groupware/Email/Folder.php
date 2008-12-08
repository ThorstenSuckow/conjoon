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
 * An email folder represents an abstract storage location for email-items. It defines itself as
 * an object with the following properties:
 *
 *  id  : The id of the folder
 *  name : the name of the folder
 *  isChildAllowed : wether or not this folder may have child folders
 *  isLocked : wether or not this folder is locked for editing
 *  type : the storage type the folder represents. This can be any of "inbox
 *  "outbox, "draft", "spam", "sent", "folder"
 *  childCount : The number of child folders this folder has, if any
 *  pendingCount : Depending on the folder type, "pending" denotes either the
 *  number of unread emails, or the total number of emails in this folder
 *
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon_Groupware
 * @package    Conjoon_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Conjoon_Modules_Groupware_Email_Folder implements Conjoon_BeanContext, Serializable {

    private $id;
    private $name;
    private $isChildAllowed;
    private $isLocked;
    private $type;
    private $childCount;
    private $pendingCount;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getName(){return $this->name;}
    public function isChildAllowed(){return $this->isChildAllowed;}
    public function isLocked(){return $this->isLocked;}
    public function getType(){return $this->type;}
    public function getChildCount(){return $this->childCount;}
    public function getPendingCount(){return $this->pendingCount;}


    public function setId($id){$this->id = $id;}
    public function setName($name){$this->name = $name;}
    public function setChildAllowed($isChildAllowed){$this->isChildAllowed = $isChildAllowed;}
    public function setLocked($isLocked){$this->isLocked = $isLocked;}
    public function setType($type){$this->type = $type;}
    public function setChildCount($childCount){$this->childCount = $childCount;}
    public function setPendingCount($pendingCount){$this->pendingCount = $pendingCount;}



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
     * @return Conjoon_Groupware_Email_AccountDto
     */
    public function getDto()
    {
        require_once 'Folder/Dto.php';

        $data = $this->toArray();

        $dto = new Conjoon_Modules_Groupware_Email_Folder_Dto();
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
            'id'             => $this->id,
            'name'           => $this->name,
            'isChildAllowed' => $this->isChildAllowed,
            'isLocked'       => $this->isLocked,
            'type'           => $this->type,
            'childCount'     => $this->childCount,
            'pendingCount'   => $this->pendingCount
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