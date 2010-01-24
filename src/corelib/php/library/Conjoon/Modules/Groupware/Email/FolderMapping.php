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
 * Conjoon_BeanContext
 */
require_once 'Conjoon/BeanContext.php';


/**
 * A class representing a folder mapping for local/IMAP folders/mailboxes.
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon_Groupware
 * @package    Conjoon_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Conjoon_Modules_Groupware_Email_FolderMapping implements Conjoon_BeanContext, Serializable {

    const INBOX  = 'INBOX';
    const OUTBOX = 'OUTBOX';
    const DRAFT  = 'DRAFT';
    const SENT   = 'SENT';
    const TRASH  = 'TRASH';

    private $id;
    private $rootFolderId;
    private $groupwareEmailAccountsId;
    private $globalName;
    private $type;
    private $name;

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getRootFolderId(){return $this->rootFolderId;}
    public function getGroupwareEmailAccountsId(){return $this->groupwareEmailAccountsId;}
    public function getGlobalName(){return $this->globalName;}
    public function getType(){return $this->type;}
    public function getName(){return $this->name;}

    public function setId($id){$this->id = $id;}
    public function setRootFolderId($rootFolderId){$this->rootFolderId = $rootFolderId;}
    public function setGroupwareEmailAccountsId($groupwareEmailAccountsId){$this->groupwareEmailAccountsId = $groupwareEmailAccountsId;}
    public function setGlobalName($globalName){$this->globalName = $globalName;}
    public function setType($type){$this->type = $type;}
    public function setName($name){$this->name = $name;}


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
     * @return Conjoon_Groupware_Email_FolderMapping
     */
    public function getDto()
    {
        require_once 'FolderMapping/Dto.php';

        $data = $this->toArray();

        $dto = new Conjoon_Modules_Groupware_Email_FolderMapping_Dto();
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
            'id'                       => $this->id,
            'rootFolderId'             => $this->rootFolderId,
            'groupwareEmailAccountsId' => $this->groupwareEmailAccountsId,
            'globalName'               => $this->globalName,
            'type'                     => $this->type,
            'name'                     => $this->name
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
        return
            'id: '.$data['id'].', '.
            'rootFolderId: '.$data['rootFolderId'].', '.
            'groupwareEmailAccountsId: '.$data['groupwareEmailAccountsId'].', '.
            'globalName: '.$data['globalName'].', '.
            'type: '.$data['type'].', ';
            'name: '.$data['name'].';';
    }
}