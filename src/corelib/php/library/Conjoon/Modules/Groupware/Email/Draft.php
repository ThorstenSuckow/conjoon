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
 * @see Conjoon_Modules_Groupware_Email_Draft_Dto
 */
require_once 'Conjoon/Modules/Groupware/Email/Draft/Dto.php';

/**
 * Class modelling an email draft, i.e. a message that is about to be send.
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon_Groupware
 * @package    Conjoon_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
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

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->to  = array();
        $this->cc  = array();
        $this->bcc = array();
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
            'inReplyTo'                => $this->inReplyTo,
            'references'               => $this->references
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
            } else {
                $strs[] = $key.': '.$value;
            }
        }
        return get_class($this).'['.implode('; ', $strs).']';
    }
}