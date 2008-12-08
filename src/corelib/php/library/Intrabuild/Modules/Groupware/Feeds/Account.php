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
 * A class representing an feed account.
 *
 * @uses       Intrabuild_BeanContext
 * @category   Intrabuild_Groupware
 * @package    Intrabuild_Groupware
 * @subpackage Feeds
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Intrabuild_Modules_Groupware_Feeds_Account implements Intrabuild_BeanContext, Serializable {

    private $id;
    private $userId;
    private $name;
    private $title;
    private $link;
    private $description;
    private $uri;
    private $updateInterval;
    private $deleteInterval;
    private $_isDeleted;
    private $_lastUpdated;

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getUserId(){return $this->userId;}
    public function getName(){return $this->name;}
    public function getTitle(){return $this->title;}
    public function getUri(){return $this->uri;}
    public function getLink(){return $this->link;}
    public function getDescription(){return $this->description;}
    public function getUpdateInterval(){return $this->updateInterval;}
    public function getDeleteInterval(){return $this->deleteInterval;}
    public function getLastUpdated(){return $this->_lastUpdated;}
    public function isDeleted(){return $this->_isDeleted;}

    public function setLastUpdated($lastUpdated){$this->_lastUpdated = $lastUpdated;}
    public function setId($id){$this->id = $id;}
    public function setUserId($userId){$this->userId = $userId;}
    public function setName($name){$this->name = $name;}
    public function setTitle($title){$this->title = $title;}
    public function setLink($link){$this->link = $link;}
    public function setDescription($description){$this->description = $description;}
    public function setUri($uri){$this->uri = $uri;}
    public function setUpdateInterval($updateInterval){$this->updateInterval = $updateInterval;}
    public function setDeleteInterval($deleteInterval){$this->deleteInterval = $deleteInterval;}
    public function setDeleted($isDeleted){$this->_isDeleted = $isDeleted;}

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
     * @return Intrabuild_Groupware_Feeds_AccountDto
     */
    public function getDto()
    {
        require_once 'Account/Dto.php';

        $data = $this->toArray();

        $dto = new Intrabuild_Modules_Groupware_Feeds_Account_Dto();
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
            'userId'         => $this->userId,
            'name'           => $this->name,
            'title'          => $this->title,
            'link'           => $this->link,
            'description'    => $this->description,
            'uri'            => $this->uri,
            'updateInterval' => $this->updateInterval,
            'deleteInterval' => $this->deleteInterval
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
            'userId: '.$data['userId'].', '.
            'name: '.$data['name'].', '.
            'title: '.$data['title'].', '.
            'uri: '.$data['uri'].', '.
            'link: '.$data['link'].', '.
            'description: '.$data['description'].', '.
            'updateInterval: '.$data['updateInterval'].', '.
            'deleteInterval: '.$data['deleteInterval'].';';
    }
}