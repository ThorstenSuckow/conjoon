<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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
 * @uses       Intrabuild_BeanContext
 * @category   Intrabuild_Groupware
 * @package    Intrabuild_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Intrabuild_Modules_Groupware_Email_Message implements Intrabuild_BeanContext, Serializable {

    private $id;
    private $to;
    private $cc;
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
    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getTo(){return $this->to;}
    public function getCc(){return $this->cc;}
    public function getFrom(){return $this->from;}
    public function getBody(){return $this->body;}
    public function getSubject(){return $this->subject;}
    public function getDate(){return $this->date;}
    public function isSpam(){return $this->isSpam;}
    public function isPlainText(){return $this->isPlainText;}
    public function getAttachments(){return $this->attachments;}
    public function getGroupwareEmailFoldersId(){return $this->groupwareEmailFoldersId;}

    public function setId($id){$this->id = $id;}
    public function setTo($to){$this->to = $to;}
    public function setBody($body){$this->body = $body;}
    public function setCc($cc){$this->cc = $cc;}
    public function setFrom($from){$this->from = $from;}
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

// -------- interface Intrabuild_BeanContext

    /**
     * Returns a Dto for an instance of this class.
     *
     * @return Intrabuild_Groupware_Email_AccountDto
     */
    public function getDto()
    {
        require_once 'Message/Dto.php';

        $data = $this->toArray();

        $dto = new Intrabuild_Modules_Groupware_Email_Message_Dto();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                if ($key == 'attachments') {
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
            'to'           => $this->to,
            'cc'           => $this->cc,
            'from'         => $this->from,
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
            if ($key == 'attachments') {
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