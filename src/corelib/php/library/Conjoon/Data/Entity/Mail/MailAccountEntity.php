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
 * @see \Conjoon\Data\Entity\DataEntity
 */
require_once 'Conjoon/Data/Entity/DataEntity.php';

/**
 * Interface all MailAccountEntity entities have to implement.
 *
 * @category   Conjoon_Data
 * @package    Entity
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MailAccountEntity extends \Conjoon\Data\Entity\DataEntity {


    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set name
     *
     * @param string $name
     * @return MailAccountEntity
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set address
     *
     * @param string $address
     * @return MailAccountEntity
     */
    public function setAddress($address);

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress();

    /**
     * Set replyAddress
     *
     * @param string $replyAddress
     * @return MailAccountEntity
     */
    public function setReplyAddress($replyAddress);

    /**
     * Get replyAddress
     *
     * @return string
     */
    public function getReplyAddress();

    /**
     * Set isStandard
     *
     * @param boolean $isStandard
     * @return MailAccountEntity
     */
    public function setIsStandard($isStandard);

    /**
     * Get isStandard
     *
     * @return boolean
     */
    public function getIsStandard();

    /**
     * Set protocol
     *
     * @param string $protocol
     * @return MailAccountEntity
     */
    public function setProtocol($protocol);

    /**
     * Get protocol
     *
     * @return string
     */
    public function getProtocol();

    /**
     * Set serverInbox
     *
     * @param string $serverInbox
     * @return MailAccountEntity
     */
    public function setServerInbox($serverInbox);

    /**
     * Get serverInbox
     *
     * @return string
     */
    public function getServerInbox();

    /**
     * Set serverOutbox
     *
     * @param string $serverOutbox
     * @return MailAccountEntity
     */
    public function setServerOutbox($serverOutbox);

    /**
     * Get serverOutbox
     *
     * @return string
     */
    public function getServerOutbox();

    /**
     * Set usernameInbox
     *
     * @param string $usernameInbox
     * @return MailAccountEntity
     */
    public function setUsernameInbox($usernameInbox);

    /**
     * Get usernameInbox
     *
     * @return string
     */
    public function getUsernameInbox();

    /**
     * Set usernameOutbox
     *
     * @param string $usernameOutbox
     * @return MailAccountEntity
     */
    public function setUsernameOutbox($usernameOutbox);

    /**
     * Get usernameOutbox
     *
     * @return string
     */
    public function getUsernameOutbox();

    /**
     * Set userName
     *
     * @param string $userName
     * @return MailAccountEntity
     */
    public function setUserName($userName);

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName();

    /**
     * Set isOutboxAuth
     *
     * @param boolean $isOutboxAuth
     * @return MailAccountEntity
     */
    public function setIsOutboxAuth($isOutboxAuth);

    /**
     * Get isOutboxAuth
     *
     * @return boolean
     */
    public function getIsOutboxAuth();

    /**
     * Set passwordInbox
     *
     * @param string $passwordInbox
     * @return MailAccountEntity
     */
    public function setPasswordInbox($passwordInbox);

    /**
     * Get passwordInbox
     *
     * @return string
     */
    public function getPasswordInbox();

    /**
     * Set passwordOutbox
     *
     * @param string $passwordOutbox
     * @return MailAccountEntity
     */
    public function setPasswordOutbox($passwordOutbox);

    /**
     * Get passwordOutbox
     *
     * @return string
     */
    public function getPasswordOutbox();

    /**
     * Set signature
     *
     * @param string $signature
     * @return MailAccountEntity
     */
    public function setSignature($signature);

    /**
     * Get signature
     *
     * @return string
     */
    public function getSignature();

    /**
     * Set isSignatureUsed
     *
     * @param boolean $isSignatureUsed
     * @return MailAccountEntity
     */
    public function setIsSignatureUsed($isSignatureUsed);

    /**
     * Get isSignatureUsed
     *
     * @return boolean
     */
    public function getIsSignatureUsed();

    /**
     * Set portInbox
     *
     * @param integer $portInbox
     * @return MailAccountEntity
     */
    public function setPortInbox($portInbox);

    /**
     * Get portInbox
     *
     * @return integer
     */
    public function getPortInbox();

    /**
     * Set portOutbox
     *
     * @param integer $portOutbox
     * @return MailAccountEntity
     */
    public function setPortOutbox($portOutbox);

    /**
     * Get portOutbox
     *
     * @return integer
     */
    public function getPortOutbox();

    /**
     * Set inboxConnectionType
     *
     * @param string $inboxConnectionType
     * @return MailAccountEntity
     */
    public function setInboxConnectionType($inboxConnectionType);

    /**
     * Get inboxConnectionType
     *
     * @return string
     */
    public function getInboxConnectionType();

    /**
     * Set outboxConnectionType
     *
     * @param string $outboxConnectionType
     * @return MailAccountEntity
     */
    public function setOutboxConnectionType($outboxConnectionType);

    /**
     * Get outboxConnectionType
     *
     * @return string
     */
    public function getOutboxConnectionType();

    /**
     * Set isCopyLeftOnServer
     *
     * @param boolean $isCopyLeftOnServer
     * @return MailAccountEntity
     */
    public function setIsCopyLeftOnServer($isCopyLeftOnServer);

    /**
     * Get isCopyLeftOnServer
     *
     * @return boolean
     */
    public function getIsCopyLeftOnServer();

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return MailAccountEntity
     */
    public function setIsDeleted($isDeleted);

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted();

    /**
     * Set users
     *
     * @param \Conjoon\Data\Entity\User\UserEntity $users
     * @return MailAccountEntity
     */
    public function setUser(\Conjoon\Data\Entity\User\UserEntity $users);

    /**
     * Get users
     *
     * @return Conjoon\Data\Entity\User\UserEntity
     */
    public function getUser();


    /**
     * Add folderMappings
     *
     * @param Conjoon\Data\Entity\Mail\FolderMappingEntity $folderMappings
     * @return DefaultMailAccountEntity
     */
    public function addFolderMapping(
        \Conjoon\Data\Entity\Mail\FolderMappingEntity $folderMapping);

    /**
     * Remove folderMappings
     *
     * @param Conjoon\Data\Entity\Mail\FolderMappingEntity $folderMappings
     */
    public function removeFolderMapping(
        \Conjoon\Data\Entity\Mail\FolderMappingEntity $folderMapping);

    /**
     * Get folderMappings
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getFolderMappings();

}