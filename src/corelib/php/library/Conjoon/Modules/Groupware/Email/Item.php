<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
 * Conjoon_BeanContext
 */
require_once 'Conjoon/BeanContext.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_Dto
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/Dto.php';

/**
 * An email item defines itself as a collection of data from the emails header,
 * such as
 *
 *  from
 *  subject
 *  date (delivery date)
 *
 * to, cc, and bcc will be grouped together in the field "recipients".
 *
 * Additionally, a few other properties will be set, which will help to identify
 * the properties of the email represented by the item:
 *
 * isAttachment -> wether the email item has attachments or not
 * isRead -> wether or not the email was read by the current user viewing the email
 * isSpam -> wether or not the email was marked as spam by the current user
 * isDraft -> wether or not the email item is a draft created by a user, i.e. an email
 * that is being written and will be send later on
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon_Groupware
 * @package    Conjoon_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */

class Conjoon_Modules_Groupware_Email_Item implements Conjoon_BeanContext, Serializable {

    private $id;
    private $recipients;
    private $sender;
    private $subject;
    private $date;
    private $isRead;
    private $isAttachment;
    private $isSpam;
    private $isDraft;
    private $isOutboxPending;
    private $groupwareEmailFoldersId;
    private $path;
    private $referencedAsTypes;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->referencedAsTypes = array();
        $this->path              = array();
    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getPath(){return $this->path;}
    public function getRecipients(){return $this->recipients;}
    public function getSender(){return $this->sender;}
    public function getSubject(){return $this->subject;}
    public function getReferencedAsTypes(){return $this->referencedAsTypes;}
    public function getDate(){return $this->date;}
    public function isRead(){return $this->isRead;}
    public function isAttachment(){return $this->isAttachment;}
    public function isSpam(){return $this->isSpam;}
    public function isDraft(){return $this->isDraft;}
    public function isOutboxPending(){return $this->isOutboxPending;}
    public function getGroupwareEmailFoldersId(){return $this->groupwareEmailFoldersId;}

    public function setId($id){$this->id = $id;}
    public function setRecipients($recipients){$this->recipients = $recipients;}
    public function setSender($sender){$this->sender = $sender;}
    public function setSubject($subject){$this->subject = $subject;}
    public function setReferencedAsTypes(Array $referencedAsTypes){$this->referencedAsTypes = $referencedAsTypes;}
    public function setPath(Array $path = null){$this->path = $path ? $path : array();}
    public function setDate($date){$this->date = $date;}
    public function setRead($isRead){$this->isRead = $isRead;}
    public function setAttachment($isAttachment){$this->isAttachment = $isAttachment;}
    public function setSpam($isSpam){$this->isSpam = $isSpam;}
    public function setDraft($isDraft){$this->isDraft = $isDraft;}
    public function setOutboxPending($isOutboxPending){$this->isOutboxPending = $isOutboxPending;}
    public function setGroupwareEmailFoldersId($groupwareEmailFoldersId){$this->groupwareEmailFoldersId = $groupwareEmailFoldersId;}


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

        $dto = new Conjoon_Modules_Groupware_Email_Item_Dto();
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
            'id'                => $this->id,
            'recipients'        => $this->recipients,
            'sender'            => $this->sender,
            'subject'           => $this->subject,
            'date'              => $this->date,
            'isRead'            => $this->isRead,
            'isAttachment'      => $this->isAttachment,
            'isSpam'            => $this->isSpam,
            'isDraft'           => $this->isDraft,
            'isOutboxPending'   => $this->isOutboxPending,
            'referencedAsTypes' => $this->referencedAsTypes,
            'path'              => $this->path,
            'groupwareEmailFoldersId' => $this->groupwareEmailFoldersId
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
            if ($key == 'referencedAsTypes') {
                $strs[] = 'referencedAsTypes: [' . implode(',', $value) . ']';
            } else {
                $strs[] = $key.': '.$value;
            }
        }
        return get_class($this).'['.implode('; ', $strs).']';
    }
}