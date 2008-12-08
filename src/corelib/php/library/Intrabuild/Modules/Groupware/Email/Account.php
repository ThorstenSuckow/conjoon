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
 * A class representing an email account.
 * A collection of properties needed when communicating with
 * POP3/IMAP/SMTP-servers.
 *
 * @uses       Intrabuild_BeanContext
 * @category   Intrabuild_Groupware
 * @package    Intrabuild_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Intrabuild_Modules_Groupware_Email_Account implements Intrabuild_BeanContext, Serializable {

    const PROTOCOL_POP3 = 'POP3';
    const PROTOCOL_IMAP = 'IMAP';

    const PORT_POP3 = 110;
    const PORT_IMAP = 110;
    const PORT_SMTP = 25;

    private $id;
    private $userId;
    private $name;
    private $address;
    private $replyAddress;
    private $isStandard;
    private $protocol;
    private $serverInbox;
    private $serverOutbox;
    private $usernameInbox;
    private $usernameOutbox;
    private $userName;
    private $isOutboxAuth;
    private $passwordInbox;
    private $passwordOutbox;
    private $signature;
    private $isSignatureUsed;
    private $portInbox;
    private $portOutbox;
    private $isCopyLeftOnServer;
    private $_isDeleted;

    /**
     * Constructor.
     *
     * Sets <tt>portInbox</tt>, <tt>portOutbox</tt> and <tt>protocol</tt>
     * to their appropriate default values.
     *
     * @see setPortInbox
     * @see setPortOutbox
     * @see setProtocol
     */
    public function __construct()
    {
        $this->setPortInbox(self::PORT_POP3);
        $this->setPortOutbox(self::PORT_SMTP);
        $this->setProtocol(self::PROTOCOL_POP3);
        $this->setCopyLeftonServer(true);
    }

// -------- accessors

    public function getId(){return $this->id;}
    public function getUserId(){return $this->userId;}
    public function getName(){return $this->name;}
    public function getAddress(){return $this->address;}
    public function getReplyAddress(){return $this->replyAddress;}
    public function isStandard(){return $this->isStandard;}
    public function getProtocol(){return $this->protocol;}
    public function getServerInbox(){return $this->serverInbox;}
    public function getServerOutbox(){return $this->serverOutbox;}
    public function getUsernameInbox(){return $this->usernameInbox;}
    public function getUsernameOutbox(){return $this->usernameOutbox;}
    public function getUserName(){return $this->userName;}
    public function isOutboxAuth(){return $this->isOutboxAuth;}
    public function getPasswordInbox(){return $this->passwordInbox;}
    public function getPasswordOutbox(){return $this->passwordOutbox;}
    public function getSignature(){return $this->signature;}
    public function isSignatureUsed(){return $this->isSignatureUsed;}
    public function getPortInbox(){return $this->portInbox;}
    public function getPortOutbox(){return $this->portOutbox;}
    public function isCopyLeftOnServer(){return $this->isCopyLeftOnServer;}
    public function isDeleted(){return $this->_isDeleted;}

    public function setId($id){$this->id = $id;}
    public function setUserId($userId){$this->userId = $userId;}
    public function setName($name){$this->name = $name;}
    public function setAddress($address){$this->address = $address;}
    public function setReplyAddress($replyAddress){$this->replyAddress = $replyAddress;}
    public function setStandard($isStandard){$this->isStandard = $isStandard;}
    public function setProtocol($protocol){$this->protocol = $protocol;}
    public function setServerInbox($serverInbox){$this->serverInbox = $serverInbox;}
    public function setServerOutbox($serverOutbox){$this->serverOutbox = $serverOutbox;}
    public function setUsernameInbox($usernameInbox){$this->usernameInbox = $usernameInbox;}
    public function setUsernameOutbox($usernameOutbox){$this->usernameOutbox = $usernameOutbox;}
    public function setUserName($userName){$this->userName = $userName;}
    public function setOutboxAuth($isOutboxAuth){$this->isOutboxAuth = $isOutboxAuth;}
    public function setPasswordInbox($passwordInbox){$this->passwordInbox = $passwordInbox;}
    public function setPasswordOutbox($passwordOutbox){$this->passwordOutbox = $passwordOutbox;}
    public function setSignature($signature){$this->signature = $signature;}
    public function setSignatureUsed($isSignatureUsed){$this->isSignatureUsed = $isSignatureUsed;}

    public function setPortInbox($portInbox)
    {
        if ($portInbox < 0 || $portInbox > 65535) {
            throw new OutOfRangeException("Port number for inbox server out of range: ".$portInbox);
        }
        $this->portInbox = $portInbox;
    }

    public function setPortOutbox($portOutbox)
    {
        if ($portOutbox < 0 || $portOutbox > 65535) {
            throw new OutOfRangeException("Port number for outbox server out of range: ".$portOutbox);
        }
        $this->portOutbox = $portOutbox;
    }

    public function setCopyLeftOnServer($isCopyLeftOnServer){$this->isCopyLeftOnServer = $isCopyLeftOnServer;}
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
     * @return Intrabuild_Groupware_Email_AccountDto
     */
    public function getDto()
    {
        require_once 'Account/Dto.php';

        $data = $this->toArray();

        $dto = new Intrabuild_Modules_Groupware_Email_Account_Dto();
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
            'id'                 => $this->id,
            'userId'             => $this->userId,
            'name'               => $this->name,
            'address'            => $this->address,
            'replyAddress'       => $this->replyAddress,
            'isStandard'         => $this->isStandard,
            'protocol'           => $this->protocol,
            'serverInbox'        => $this->serverInbox,
            'serverOutbox'       => $this->serverOutbox,
            'usernameInbox'      => $this->usernameInbox,
            'usernameOutbox'     => $this->usernameOutbox,
            'userName'           => $this->userName,
            'isOutboxAuth'       => $this->isOutboxAuth,
            'passwordInbox'      => $this->passwordInbox,
            'passwordOutbox'     => $this->passwordOutbox,
            'signature'          => $this->signature,
            'isSignatureUsed'    => $this->isSignatureUsed,
            'portInbox'          => $this->portInbox,
            'portOutbox'         => $this->portOutbox,
            'isCopyLeftOnServer' => $this->isCopyLeftOnServer
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
            'address: '.$data['address'].', '.
            'replyAddress: '.$data['replyAddress'].', '.
            'isStandard: '.$data['isStandard'].', '.
            'protocol: '.$data['protocol'].', '.
            'serverInbox: '.$data['serverInbox'].', '.
            'serverOutbox: '.$data['serverOutbox'].', '.
            'usernameInbox: '.$data['usernameInbox'].', '.
            'usernameOutbox: '.$data['usernameOutbox'].', '.
            'userName: '.$data['userName'].', '.
            'isOutboxAuth: '.$data['isOutboxAuth'].', '.
            'passwordInbox: '.$data['passwordInbox'].', '.
            'passwordOutbox: '.$data['passwordOutbox'].', '.
            'signature: '.$data['signature'].', '.
            'isSignatureUsed: '.$data['isSignatureUsed'].', '.
            'portInbox: '.$data['portInbox'].', '.
            'portOutbox: '.$data['portOutbox'].', '.
            'isCopyLeftOnServer: '.$data['isCopyLeftOnServer'].';';
    }
}