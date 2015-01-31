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
 * @see Conjoon_Modules_Groupware_Email_Address_List
 */
require_once 'Conjoon/Modules/Groupware/Email/Address/List.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Message_Dto
 */
require_once 'Conjoon/Modules/Groupware/Email/Message/Dto.php';


/**
 * An email item defines itself as a collection of data from the emails header,
 * such as
 *  to
 *  cc
 *  from
 *  subject
 *  date (delivery date)
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

class Conjoon_Modules_Groupware_Email_Message implements Conjoon_BeanContext, Serializable {

    private $id;
    private $uId;
    private $path;
    private $to;
    private $cc;
    private $bcc;
    private $replyTo;
    private $from;
    private $subject;
    private $date;
    private $isSpam;
    private $isPlainText;
    private $attachments;
    private $body;
    private $groupwareEmailFoldersId;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->attachments = array();

        $this->from    = new Conjoon_Modules_Groupware_Email_Address_List();
        $this->replyTo = new Conjoon_Modules_Groupware_Email_Address_List();
        $this->to      = new Conjoon_Modules_Groupware_Email_Address_List();
        $this->cc      = new Conjoon_Modules_Groupware_Email_Address_List();
        $this->bcc     = new Conjoon_Modules_Groupware_Email_Address_List();
    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getUId(){return $this->uId;}
    public function getPath(){return $this->path;}
    public function getTo(){return $this->to;}
    public function getCc(){return $this->cc;}
    public function getBcc(){return $this->bcc;}
    public function getFrom(){return $this->from;}
    public function getReplyTo(){return $this->replyTo;}
    public function getBody(){return $this->body;}
    public function getSubject(){return $this->subject;}
    public function getDate(){return $this->date;}
    public function isSpam(){return $this->isSpam;}
    public function isPlainText(){return $this->isPlainText;}
    public function getAttachments(){return $this->attachments;}
    public function getGroupwareEmailFoldersId(){return $this->groupwareEmailFoldersId;}

    public function setId($id){$this->id = $id;}
    public function setUId($uId){$this->uId = $uId;}
    public function setPath($path){$this->path = $path;}
    public function setTo(Conjoon_Modules_Groupware_Email_Address_List $to){$this->to = $to;}
    public function setBcc(Conjoon_Modules_Groupware_Email_Address_List $bcc){$this->bcc = $bcc;}
    public function setBody($body){$this->body = $body;}
    public function setReplyTo(Conjoon_Modules_Groupware_Email_Address_List $replyTo){$this->replyTo = $replyTo;}
    public function setCc(Conjoon_Modules_Groupware_Email_Address_List $cc){$this->cc = $cc;}
    public function setFrom(Conjoon_Modules_Groupware_Email_Address_List $from){$this->from = $from;}
    public function setSubject($subject){$this->subject = $subject;}
    public function setDate($date){$this->date = $date;}
    public function setAttachments(array $attachments){$this->attachments = $attachments;}
    public function setSpam($isSpam){$this->isSpam = $isSpam;}
    public function setPlainText($isPlainText){$this->isPlainText = $isPlainText;}
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

        $dto = new Conjoon_Modules_Groupware_Email_Message_Dto();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                if ($key == 'from') {
                    $dto->$key = $this->from->toArray();
                } else if ($key == 'replyTo') {
                    $dto->$key = $this->replyTo->toArray();
                } else if ($key == 'to') {
                    $dto->$key = $this->to->toArray();
                } else if ($key == 'cc') {
                    $dto->$key = $this->cc->toArray();
                } else if ($key == 'bcc') {
                    $dto->$key = $this->bcc->toArray();
                } else if ($key == 'attachments') {
                    $attachments = array();
                    for ($i = 0; $i < count($this->attachments); $i++) {
                        $attachments[] = $this->attachments[$i]->getDto();
                    }
                    $dto->$key = $attachments;
                } else {
                    $dto->$key = $value;
                }
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
        $attachments = array();
        for ($i = 0; $i < count($this->attachments); $i++) {
            $attachments[] = $this->attachments[$i]->toArray();
        }

        return array(
            'id'           => $this->id,
            'uId'          => $this->uId,
            'path'         => $this->path,
            'to'           => $this->cc->toArray(),
            'cc'           => $this->cc->toArray(),
            'bcc'          => $this->bcc->toArray(),
            'replyTo'      => $this->replyTo->toArray(),
            'from'         => $this->from->toArray(),
            'body'         => $this->body,
            'subject'      => $this->subject,
            'isPlainText'  => $this->isPlainText,
            'date'         => $this->date,
            'attachments'  => $attachments,
            'isSpam'       => $this->isSpam,
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
            if ($key == 'from') {
                $strs[] = 'from: ['.$this->from->__toString().']';
            } else if ($key == 'to') {
                $strs[] = 'to: ['.$this->to->_toString.']';
            } else if ($key == 'cc') {
                $strs[] = 'cc: ['.$this->cc->__toString().']';
            } else if ($key == 'replyTo') {
                $strs[] = 'replyTo: ['.$this->replyTo->__toString().']';
            } else if ($key == 'bcc') {
                $strs[] = 'bcc: ['.$this->bcc->__toString().']';
            } else if ($key == 'path') {
                $strs[] = 'path: ['.json_encode($this->path).']';
            } else if ($key == 'attachments') {
                $attachments = array();
                for ($i = 0; $i < count($this->attachments); $i++) {
                    $attachments[] = $this->attachments[$i]->__toString();
                }
                $strs[] = 'attachments: ['.implode(';', $attachments).']';
            } else {
                $strs[] = $key.': '.$value;
            }
        }
        return get_class($this).'['.implode('; ', $strs).']';
    }
}
