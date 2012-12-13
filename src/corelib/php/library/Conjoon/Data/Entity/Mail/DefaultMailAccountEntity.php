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


namespace Conjoon\Data\Entity\Mail;

/**
 * @see \Conjoon\Data\Entity\Mail\MailAccountEntity
 */
require_once 'Conjoon/Data/Entity/Mail/MailAccountEntity.php';

/**
 * Interface all MailAccountEntity entities have to implement.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailAccountEntity implements MailAccountEntity {


    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $address
     */
    private $address;

    /**
     * @var string $replyAddress
     */
    private $replyAddress;

    /**
     * @var boolean $isStandard
     */
    private $isStandard;

    /**
     * @var string $protocol
     */
    private $protocol;

    /**
     * @var string $serverInbox
     */
    private $serverInbox;

    /**
     * @var string $serverOutbox
     */
    private $serverOutbox;

    /**
     * @var string $usernameInbox
     */
    private $usernameInbox;

    /**
     * @var string $usernameOutbox
     */
    private $usernameOutbox;

    /**
     * @var string $userName
     */
    private $userName;

    /**
     * @var boolean $isOutboxAuth
     */
    private $isOutboxAuth;

    /**
     * @var string $passwordInbox
     */
    private $passwordInbox;

    /**
     * @var string $passwordOutbox
     */
    private $passwordOutbox;

    /**
     * @var string $signature
     */
    private $signature;

    /**
     * @var boolean $isSignatureUsed
     */
    private $isSignatureUsed;

    /**
     * @var integer $portInbox
     */
    private $portInbox;

    /**
     * @var integer $portOutbox
     */
    private $portOutbox;

    /**
     * @var string $inboxConnectionType
     */
    private $inboxConnectionType;

    /**
     * @var string $outboxConnectionType
     */
    private $outboxConnectionType;

    /**
     * @var boolean $isCopyLeftOnServer
     */
    private $isCopyLeftOnServer;

    /**
     * @var boolean $isDeleted
     */
    private $isDeleted;

    /**
     * @var Conjoon\Data\Entity\User\UserEntity
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $folderMappings;

    /**
     * Creates a new instance of this class.
     */
    public function __construct()
    {
        $this->folderMappings = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @inheritdoc
     */
    public function setReplyAddress($replyAddress)
    {
        $this->replyAddress = $replyAddress;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReplyAddress()
    {
        return $this->replyAddress;
    }

    /**
     * @inheritdoc
     */
    public function setIsStandard($isStandard)
    {
        $this->isStandard = $isStandard;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsStandard()
    {
        return $this->isStandard;
    }

    /**
     * @inheritdoc
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @inheritdoc
     */
    public function setServerInbox($serverInbox)
    {
        $this->serverInbox = $serverInbox;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getServerInbox()
    {
        return $this->serverInbox;
    }

    /**
     * @inheritdoc
     */
    public function setServerOutbox($serverOutbox)
    {
        $this->serverOutbox = $serverOutbox;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getServerOutbox()
    {
        return $this->serverOutbox;
    }

    /**
     * @inheritdoc
     */
    public function setUsernameInbox($usernameInbox)
    {
        $this->usernameInbox = $usernameInbox;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUsernameInbox()
    {
        return $this->usernameInbox;
    }

    /**
     * @inheritdoc
     */
    public function setUsernameOutbox($usernameOutbox)
    {
        $this->usernameOutbox = $usernameOutbox;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUsernameOutbox()
    {
        return $this->usernameOutbox;
    }

    /**
     * @inheritdoc
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @inheritdoc
     */
    public function setIsOutboxAuth($isOutboxAuth)
    {
        $this->isOutboxAuth = $isOutboxAuth;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsOutboxAuth()
    {
        return $this->isOutboxAuth;
    }

    /**
     * @inheritdoc
     */
    public function setPasswordInbox($passwordInbox)
    {
        $this->passwordInbox = $passwordInbox;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPasswordInbox()
    {
        return $this->passwordInbox;
    }

    /**
     * @inheritdoc
     */
    public function setPasswordOutbox($passwordOutbox)
    {
        $this->passwordOutbox = $passwordOutbox;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPasswordOutbox()
    {
        return $this->passwordOutbox;
    }

    /**
     * @inheritdoc
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @inheritdoc
     */
    public function setIsSignatureUsed($isSignatureUsed)
    {
        $this->isSignatureUsed = $isSignatureUsed;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsSignatureUsed()
    {
        return $this->isSignatureUsed;
    }

    /**
     * @inheritdoc
     */
    public function setPortInbox($portInbox)
    {
        $this->portInbox = $portInbox;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPortInbox()
    {
        return $this->portInbox;
    }

    /**
     * @inheritdoc
     */
    public function setPortOutbox($portOutbox)
    {
        $this->portOutbox = $portOutbox;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPortOutbox()
    {
        return $this->portOutbox;
    }

    /**
     * @inheritdoc
     */
    public function setInboxConnectionType($inboxConnectionType)
    {
        $this->inboxConnectionType = $inboxConnectionType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getInboxConnectionType()
    {
        return $this->inboxConnectionType;
    }

    /**
     * @inheritdoc
     */
    public function setOutboxConnectionType($outboxConnectionType)
    {
        $this->outboxConnectionType = $outboxConnectionType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOutboxConnectionType()
    {
        return $this->outboxConnectionType;
    }

    /**
     * @inheritdoc
     */
    public function setIsCopyLeftOnServer($isCopyLeftOnServer)
    {
        $this->isCopyLeftOnServer = $isCopyLeftOnServer;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsCopyLeftOnServer()
    {
        return $this->isCopyLeftOnServer;
    }

    /**
     * @inheritdoc
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @inheritdoc
     */
    public function setUser(\Conjoon\Data\Entity\User\UserEntity $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function addFolderMapping(
        \Conjoon\Data\Entity\Mail\FolderMappingEntity $folderMapping)
    {
        $this->folderMappings[] = $folderMapping;

        return $this;
    }


    /**
     * @inheritdoc
     */
    public function removeFolderMapping(
        \Conjoon\Data\Entity\Mail\FolderMappingEntity $folderMapping)
    {
        $this->folderMappings->removeElement($folderMapping);
    }

    /**
     * @inheritdoc
     */
    public function getFolderMappings()
    {
        return $this->folderMappings;
    }


}