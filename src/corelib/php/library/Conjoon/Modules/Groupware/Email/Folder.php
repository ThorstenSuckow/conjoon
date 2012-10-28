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
 * @see Conjoon_Modules_Groupware_Email_Folder_Dto
 */
require_once 'Conjoon/Modules/Groupware/Email/Folder/Dto.php';

/**
 * An email folder represents an abstract storage location for email-items. It defines itself as
 * an object with the following properties:
 *
 *  id  : The id of the folder
 *  idForPath  : As special id from which a path can be build, needed for
 *  imap protocol.
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
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */

class Conjoon_Modules_Groupware_Email_Folder implements Conjoon_BeanContext, Serializable {

    private $id;
    private $idForPath;
    private $name;
    private $isChildAllowed;
    private $isLocked;
    private $type;
    private $childCount;
    private $pendingCount;
    private $isSelectable;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getIdForPath(){return $this->idForPath;}
    public function getName(){return $this->name;}
    public function isChildAllowed(){return $this->isChildAllowed;}
    public function isLocked(){return $this->isLocked;}
    public function getType(){return $this->type;}
    public function getChildCount(){return $this->childCount;}
    public function getPendingCount(){return $this->pendingCount;}
    public function isSelectable(){return $this->isSelectable;}


    public function setId($id){$this->id = $id;}
    public function setIdForPath($idForPath){$this->idForPath = $idForPath;}
    public function setName($name){$this->name = $name;}
    public function setChildAllowed($isChildAllowed){$this->isChildAllowed = $isChildAllowed;}
    public function setLocked($isLocked){$this->isLocked = $isLocked;}
    public function setType($type){$this->type = $type;}
    public function setChildCount($childCount){$this->childCount = $childCount;}
    public function setPendingCount($pendingCount){$this->pendingCount = $pendingCount;}
    public function setSelectable($isSelectable){$this->isSelectable = $isSelectable;}



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
            'idForPath'      => $this->idForPath,
            'name'           => $this->name,
            'isChildAllowed' => $this->isChildAllowed,
            'isLocked'       => $this->isLocked,
            'type'           => $this->type,
            'childCount'     => $this->childCount,
            'pendingCount'   => $this->pendingCount,
            'isSelectable'   => $this->isSelectable
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