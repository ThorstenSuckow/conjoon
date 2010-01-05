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
 * @see Conjoon_Modules_Groupware_Feeds_Item_Dto
 */
require_once 'Conjoon/Modules/Groupware/Feeds/Item/Dto.php';

/**
 * A class representing an feed item.
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon_Groupware
 * @package    Conjoon_Groupware
 * @subpackage Feeds
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Conjoon_Modules_Groupware_Feeds_Item implements Conjoon_BeanContext, Serializable {

    protected $id;
    protected $groupwareFeedsAccountsId;
    protected $name;
    protected $title;
    protected $author;
    protected $authorUri;
    protected $authorEmail;
    protected $description;
    protected $content;
    protected $pubDate;
    protected $link;
    protected $isRead;
    protected $guid;
    protected $savedTimestamp;


    /**
     * Constructor.
     *
     */
    public function __construct()
    {
    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getGuid(){return $this->guid;}
    public function getGroupwareFeedsAccountsId(){return $this->groupwareFeedsAccountsId;}
    public function getName(){return $this->name;}
    public function getTitle(){return $this->title;}
    public function getAuthor(){return $this->author;}
    public function getAuthorUri(){return $this->authorUri;}
    public function getAuthorEmail(){return $this->authorEmail;}
    public function getDescription(){return $this->description;}
    public function getContent(){return $this->content;}
    public function getPubDate(){return $this->pubDate;}
    public function getLink(){return $this->link;}
    public function isRead(){return $this->isRead;}
    public function getSavedTimestamp(){return $this->savedTimestamp;}

    public function setGuid($guid){$this->guid = $guid;}
    public function setId($id){$this->id = $id;}
    public function setGroupwareFeedsAccountsId($groupwareFeedsAccountsId){$this->groupwareFeedsAccountsId = $groupwareFeedsAccountsId;}
    public function setName($name){$this->name = $name;}
    public function setAuthor($author){$this->author = $author;}
    public function setAuthorUri($authorUri){$this->authorUri = $authorUri;}
    public function setAuthorEmail($authorEmail){$this->authorEmail = $authorEmail;}
    public function setContent($content){$this->content = $content;}
    public function setTitle($title){$this->title = $title;}
    public function setDescription($description){$this->description = $description;}
    public function setPubDate($pubDate){$this->pubDate = $pubDate;}
    public function setLink($link){$this->link = $link;}
    public function setRead($isRead){$this->isRead = $isRead;}
    public function setSavedTimestamp($savedTimestamp){$this->savedTimestamp = $savedTimestamp;}

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

        $dto = new Conjoon_Modules_Groupware_Feeds_Item_Dto();
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
            'groupwareFeedsAccountsId' => $this->groupwareFeedsAccountsId,
            'name' => $this->name ,
            'title' => $this->title ,
            'author' => $this->author ,
            'authorUri' => $this->authorUri,
            'authorEmail' => $this->authorEmail,
            'description' => $this->description ,
            'pubDate' => $this->pubDate,
            'guid' => $this->guid,
            'content' => $this->content,
            'link' => $this->link ,
            'isRead' => $this->isRead ,
            'savedTimestamp' => $this->savedTimestamp
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
            'groupwareFeedsAccountsId: '.$data['groupwareFeedsAccountsId'].', '.
            'name: '.$data['name'].', '.
            'title: '.$data['title'].', '.
            'author: '.$data['author'].', '.
            'authorUri: '.$data['authorUri'].', '.
            'authorEmail: '.$data['authorEmail'].', '.
            'guid: '.$data['guid'].', '.
            'content: '.htmlentities($data['content']).', '.
            'description: '.$data['description'].', '.
            'pubDate: '.$data['pubDate'].', '.
            'link: '.$data['link'].', '.
            'isRead: '.$data['isRead'].', '.
            'savedTimestamp: '.$data['savedTimestamp'].';';
    }
}