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
 * A class representing a Tweet.
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon_Service
 * @package    Conjoon_Service
 * @subpackage Twitter
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Conjoon_Modules_Service_Twitter_Tweet implements Conjoon_BeanContext, Serializable {

    private $id;
    private $text;
    private $createdAt;
    private $source;
    private $sourceUrl;
    private $truncated;
    private $userId;
    private $name;
    private $screenName;
    private $location;
    private $profileImageUrl;
    private $url;
    private $protected;
    private $description;
    private $followersCount;
    private $inReplyToStatusId;
    private $inReplyToUserId;
    private $inReplyToScreenName;
    private $favorited;



    /**
     * Constructor.
     *
     */
    public function __construct()
    {
    }

// -------- accessors

    public function setId($id){$this->id = $id;}
    public function setText($text){$this->text = $text;}
    public function setCreatedAt($createdAt){$this->createdAt = $createdAt;}
    public function setSource($source){$this->source = $source;}
    public function setSourceUrl($sourceUrl){$this->sourceUrl = $sourceUrl;}
    public function setTruncated($truncated){$this->truncated = $truncated;}
    public function setUserId($userId){$this->userId = $userId;}
    public function setName($name){$this->name = $name;}
    public function setScreenName($screenName){$this->screenName = $screenName;}
    public function setLocation($location){$this->location = $location;}
    public function setProfileImageUrl($profileImageUrl){$this->profileImageUrl = $profileImageUrl;}
    public function setUrl($url){$this->url = $url;}
    public function setProtected($protected){$this->protected = $protected;}
    public function setDescription($description){$this->description = $description;}
    public function setFollowersCount($followersCount){$this->followersCount = $followersCount;}
    public function setInReplyToStatusId($inReplyToStatusId){$this->inReplyToStatusId = $inReplyToStatusId;}
    public function setInReplyToUserId($inReplyToUserId){$this->inReplyToUserId = $inReplyToUserId;}
    public function setInReplyToScreenName($inReplyToScreenName){$this->inReplyToScreenName = $inReplyToScreenName;}
    public function setFavorited($favorited){$this->favorited = $favorited;}

    public function getId(){return $this->id;}
    public function getText(){return $this->text;}
    public function getCreatedAt(){return $this->createdAt;}
    public function getSource(){return $this->source;}
    public function getSourceUrl(){return $this->sourceUrl;}
    public function getTruncated(){return $this->truncated;}
    public function getUserId(){return $this->userId;}
    public function getName(){return $this->name;}
    public function getScreenName(){return $this->screenName;}
    public function getLocation(){return $this->location;}
    public function getProfileImageUrl(){return $this->profileImageUrl;}
    public function getUrl(){return $this->url;}
    public function isProtected(){return $this->protected;}
    public function getDescription(){return $this->description;}
    public function getFollowersCount(){return $this->followersCount;}
    public function getInReplyToStatusId(){return $this->inReplyToStatusId;}
    public function getInReplyToUserId(){return $this->inReplyToUserId;}
    public function getInReplyToScreenName(){return $this->inReplyToScreenName;}
    public function isFavorited(){return $this->favorited;}

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
     * @return Conjoon_Modules_Service_Twitter_Account_Dto
     */
    public function getDto()
    {
        require_once 'Tweet/Dto.php';

        $data = $this->toArray();

        $dto = new Conjoon_Modules_Service_Twitter_Tweet_Dto();
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
            'id'                  => $this->id,
            'text'                => $this->text,
            'createdAt'           => $this->createdAt,
            'source'              => $this->source,
            'sourceUrl'           => $this->sourceUrl,
            'truncated'           => $this->truncated,
            'userId'              => $this->userId,
            'name'                => $this->name,
            'screenName'          => $this->screenName,
            'location'            => $this->location,
            'profileImageUrl'     => $this->profileImageUrl,
            'url'                 => $this->url,
            'protected'           => $this->protected,
            'description'         => $this->description,
            'followersCount'      => $this->followersCount,
            'inReplyToStatusId'   => $this->inReplyToStatusId,
            'inReplyToUserId'     => $this->inReplyToUserId,
            'inReplyToScreenName' => $this->inReplyToScreenName,
            'favorited'           => $this->favorited
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
            '['.
            'id'                  . ': '.$this->id . ', ' .
            'text'                . ': '.$this->text . ', ' .
            'createdAt'           . ': '.$this->createdAt . ', ' .
            'source'              . ': '.$this->source . ', ' .
            'sourceUrl'           . ': '.$this->sourceUrl . ', ' .
            'truncated'           . ': '.$this->truncated . ', ' .
            'userId'              . ': '.$this->userId . ', ' .
            'name'                . ': '.$this->name . ', ' .
            'screenName'          . ': '.$this->screenName . ', ' .
            'location'            . ': '.$this->location . ', ' .
            'profileImageUrl'     . ': '.$this->profileImageUrl . ', ' .
            'url'                 . ': '.$this->url . ', ' .
            'protected'           . ': '.$this->protected . ', ' .
            'description'         . ': '.$this->description . ', ' .
            'followersCount'      . ': '.$this->followersCount . ', ' .
            'inReplyToStatusId'   . ': ' . $this->inReplyToStatusId . ', ' .
            'inReplyToUserId'     . ': ' . $this->inReplyToUserId . ', ' .
            'inReplyToScreenName' . ': ' . $this->inReplyToScreenName . ', ' .
            'favorited'           . ': ' . $this->favorited .

            ']';
    }
}