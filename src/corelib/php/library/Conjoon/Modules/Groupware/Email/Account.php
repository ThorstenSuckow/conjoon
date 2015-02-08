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
 * Conjoon_BeanContext
 */
require_once 'Conjoon/BeanContext.php';


/**
 * A class representing an email account.
 * A collection of properties needed when communicating with
 * POP3/IMAP/SMTP-servers.
 *
 * @uses       Conjoon_BeanContext
 * @category   Conjoon_Groupware
 * @package    Conjoon_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */

class Conjoon_Modules_Groupware_Email_Account implements Conjoon_BeanContext, Serializable {

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
    private $inboxConnectionType;
    private $outboxConnectionType;
    private $isCopyLeftOnServer;
    private $_isDeleted;
    private $folderMappings;
    private $_hasSeparateFolderHierarchy;
    public $localRootMailFolder;


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
        $this->folderMappings = array();

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
    public function getInboxConnectionType(){return $this->inboxConnectionType;}
    public function getOutboxConnectionType(){return $this->outboxConnectionType;}
    public function isCopyLeftOnServer(){return $this->isCopyLeftOnServer;}
    public function isDeleted(){return $this->_isDeleted;}
    public function getFolderMappings(){return $this->folderMappings;}
    public function getHasSeparateFolderHierarchy(){return $this->_hasSeparateFolderHierarchy;}
    public function getLocalRootMailFolder(){return $this->localRootMailFolder;}

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
    public function setInboxConnectionType($inboxConnectionType){$this->inboxConnectionType = $inboxConnectionType;}
    public function setOutboxConnectionType($outboxConnectionType){$this->outboxConnectionType = $outboxConnectionType;}
    public function setSignature($signature){$this->signature = $signature;}
    public function setSignatureUsed($isSignatureUsed){$this->isSignatureUsed = $isSignatureUsed;}
    public function setFolderMappings(array $folderMappings){$this->folderMappings = $folderMappings;}
    public function setHasSeparateFolderHierarchy($hasSeparateFolderHierarchy){$this->_hasSeparateFolderHierarchy = $hasSeparateFolderHierarchy;}
    public function setLocalRootMailFolder($localRootMailFolder){$this->localRootMailFolder = $localRootMailFolder;}

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

// -------- interface Conjoon_BeanContext

    /**
     * Returns a Dto for an instance of this class.
     *
     * @return Conjoon_Groupware_Email_AccountDto
     */
    public function getDto()
    {
        require_once 'Account/Dto.php';

        $data = $this->toArray();

        $dto = new Conjoon_Modules_Groupware_Email_Account_Dto();
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
            'id'                   => $this->id,
            'userId'               => $this->userId,
            'name'                 => $this->name,
            'address'              => $this->address,
            'replyAddress'         => $this->replyAddress,
            'isStandard'           => $this->isStandard,
            'protocol'             => $this->protocol,
            'serverInbox'          => $this->serverInbox,
            'serverOutbox'         => $this->serverOutbox,
            'usernameInbox'        => $this->usernameInbox,
            'usernameOutbox'       => $this->usernameOutbox,
            'userName'             => $this->userName,
            'isOutboxAuth'         => $this->isOutboxAuth,
            'passwordInbox'        => $this->passwordInbox,
            'passwordOutbox'       => $this->passwordOutbox,
            'signature'            => $this->signature,
            'isSignatureUsed'      => $this->isSignatureUsed,
            'portInbox'            => $this->portInbox,
            'portOutbox'           => $this->portOutbox,
            'inboxConnectionType'  => $this->inboxConnectionType,
            'outboxConnectionType' => $this->outboxConnectionType,
            'isCopyLeftOnServer'   => $this->isCopyLeftOnServer,
            'folderMappings'       => $this->folderMappings,
            'localRootMailFolder'  => $this->localRootMailFolder
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
            'inboxConnectionType: '.$data['inboxConnectionType'].', '.
            'outboxConnectionType: '.$data['outboxConnectionType'].', '.
            'isCopyLeftOnServer: '.$data['isCopyLeftOnServer'].', '.
            'folderMappings: '.$data['folderMappings'].', ' .
            'localRootMailFolder: ' . $data['localRootMailFolder'];
    }
}