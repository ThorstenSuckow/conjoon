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
 * @see Conjoon_Modules_Groupware_Email_Draft_Dto
 */
require_once 'Conjoon/Modules/Groupware/Email/Draft/Dto.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Attachment
 */
require_once 'Conjoon/Modules/Groupware/Email/Attachment.php';

/**
 * Class modelling an email draft, i.e. a message that is about to be send.
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon_Groupware
 * @package    Conjoon_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */

class Conjoon_Modules_Groupware_Email_Draft implements Conjoon_BeanContext, Serializable {

    private $id;
    private $groupwareEmailFoldersId;
    private $groupwareEmailAccountsId;
    private $subject;
    private $inReplyTo;
    private $references;
    private $date;
    private $contentTextPlain;
    private $contentTextHtml;
    private $to;
    private $cc;
    private $bcc;
    private $attachments;
    private $referencedData;
    private $path;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->to          = array();
        $this->cc          = array();
        $this->bcc         = array();
        $this->attachments = array();
        $this->data        = array();

        $this->referencedData = array();
    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getGroupwareEmailFoldersId(){return $this->groupwareEmailFoldersId;}
    public function getGroupwareEmailAccountsId(){return $this->groupwareEmailAccountsId;}
    public function getSubject(){return $this->subject;}
    public function getDate(){return $this->date;}
    public function getContentTextPlain(){return $this->contentTextPlain;}
    public function getContentTextHtml(){return $this->contentTextHtml;}
    public function getTo(){return $this->to;}
    public function getCc(){return $this->cc;}
    public function getBcc(){return $this->bcc;}
    public function getInReplyTo(){return $this->inReplyTo;}
    public function getReferences(){return $this->references;}
    public function getAttachments(){return $this->attachments;}
    public function getReferencedData(){return $this->referencedData;}
    public function getPath(){return $this->path;}


    public function setId($id){$this->id = $id;}
    public function setGroupwareEmailFoldersId($groupwareEmailFoldersId){$this->groupwareEmailFoldersId = $groupwareEmailFoldersId;}
    public function setGroupwareEmailAccountsId($groupwareEmailAccountsId){$this->groupwareEmailAccountsId = $groupwareEmailAccountsId;}
    public function setSubject($subject){$this->subject = $subject;}
    public function setDate($date){$this->date = $date;}
    public function setContentTextPlain($contentTextPlain){$this->contentTextPlain = $contentTextPlain;}
    public function setContentTextHtml($contentTextHtml){$this->contentTextHtml = $contentTextHtml;}
    public function setTo(Array $to){$this->to = $to;}
    public function setCc(Array $cc){$this->cc = $cc;}
    public function setBcc(Array $bcc){$this->bcc = $bcc;}
    public function setInReplyTo($inReplyTo){$this->inReplyTo = $inReplyTo;}
    public function setReferences($references){$this->references = $references;}
    public function setAttachments(array $attachments){$this->attachments = $attachments;}
    public function setReferencedData(array $referencedData){$this->referencedData = $referencedData;}
    public function setPath(array $path){$this->path = $path;}

    public function addAttachment(Conjoon_Modules_Groupware_Email_Attachment $attachment) {
        $this->attachments[] = $attachment;
    }

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
     * @return Conjoon_Groupware_Email_Draft_Dto
     */
    public function getDto()
    {
        $data = $this->toArray();

        $dto = new Conjoon_Modules_Groupware_Email_Draft_Dto();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                if ($key == 'cc') {
                    $cc = array();
                    for ($i = 0; $i < count($this->cc); $i++) {
                        $cc[] = $this->cc[$i]->getDto();
                    }
                    $dto->$key = $cc;
                } else if ($key == 'bcc') {
                    $bcc = array();
                    for ($i = 0; $i < count($this->bcc); $i++) {
                        $bcc[] = $this->bcc[$i]->getDto();
                    }
                    $dto->$key = $bcc;
                } if ($key == 'to') {
                    $to = array();
                    for ($i = 0; $i < count($this->to); $i++) {
                        $to[] = $this->to[$i]->getDto();
                    }
                    $dto->$key = $to;
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
        $cc = array();
        for ($i = 0; $i < count($this->cc); $i++) {
            $cc[] = $this->cc[$i]->toArray();
        }
        $bcc = array();
        for ($i = 0; $i < count($this->bcc); $i++) {
            $bcc[] = $this->bcc[$i]->toArray();
        }
        $to = array();
        for ($i = 0; $i < count($this->to); $i++) {
            $to[] = $this->to[$i]->toArray();
        }
        $attachments = array();
        for ($i = 0; $i < count($this->attachments); $i++) {
            $attachments[] = $this->attachments[$i]->toArray();
        }

        return array(

            'id'                       => $this->id,
            'groupwareEmailFoldersId'  => $this->groupwareEmailFoldersId,
            'groupwareEmailAccountsId' => $this->groupwareEmailAccountsId,
            'subject'                  => $this->subject,
            'date'                     => $this->date,
            'contentTextPlain'         => $this->contentTextPlain,
            'contentTextHtml'          => $this->contentTextHtml,
            'to'                       => $to,
            'cc'                       => $cc,
            'bcc'                      => $bcc,
            'attachments'              => $attachments,
            'inReplyTo'                => $this->inReplyTo,
            'references'               => $this->references,
            'referencedData'           => $this->referencedData,
            'path'                     => $this->path
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
            if ($key == 'to') {
                $to = array();
                for ($i = 0; $i < count($this->to); $i++) {
                    $to[] = $this->to[$i]->__toString();
                }
                $strs[] = 'to: ['.implode(',', $to).']';
            } else if ($key == 'cc') {
                $cc = array();
                for ($i = 0; $i < count($this->cc); $i++) {
                    $cc[] = $this->cc[$i]->__toString();
                }
                $strs[] = 'cc: ['.implode(',', $cc).']';
            } else if ($key == 'bcc') {
                $bcc = array();
                for ($i = 0; $i < count($this->bcc); $i++) {
                    $bcc[] = $this->bcc[$i]->__toString();
                }
                $strs[] = 'bcc: ['.implode(',', $bcc).']';
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