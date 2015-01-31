<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @see Conjoon_Modules_Groupware_Feeds_Account_Dto
 */
require_once 'Conjoon/Modules/Groupware/Feeds/Account/Dto.php';


/**
 * A class representing an feed account.
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon_Groupware
 * @package    Conjoon_Groupware
 * @subpackage Feeds
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */

class Conjoon_Modules_Groupware_Feeds_Account implements Conjoon_BeanContext, Serializable {

    private $id;
    private $userId;
    private $name;
    private $title;
    private $link;
    private $description;
    private $uri;
    private $updateInterval;
    private $deleteInterval;
    private $requestTimeout;
    private $isImageEnabled;
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
    public function getRequestTimeout(){return $this->requestTimeout;}
    public function getLastUpdated(){return $this->_lastUpdated;}
    public function isDeleted(){return $this->_isDeleted;}
    public function isImageEnabled(){return $this->isImageEnabled;}

    public function setRequestTimeout($requestTimeout){$this->requestTimeout = $requestTimeout;}
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
    public function setImageEnabled($isImageEnabled){$this->isImageEnabled = $isImageEnabled;}

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
     * @return Conjoon_Groupware_Feeds_AccountDto
     */
    public function getDto()
    {
        $data = $this->toArray();

        $dto = new Conjoon_Modules_Groupware_Feeds_Account_Dto();
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
            'requestTimeout' => $this->requestTimeout,
            'updateInterval' => $this->updateInterval,
            'deleteInterval' => $this->deleteInterval,
            'isImageEnabled' => $this->isImageEnabled
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
            'requestTimeout: '.$data['requestTimeout'].', '.
            'isImageEnabled: '.$data['isImageEnabled'].', '.
            'deleteInterval: '.$data['deleteInterval'].';';
    }
}