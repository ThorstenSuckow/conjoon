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


namespace Conjoon\Data\Repository\Mail;

/**
 * @see \Conjoon\Data\Repository\Mail\MessageFlagRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MessageFlagRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\ImapRepository
 */
require_once 'Conjoon/Data/Repository/Mail/ImapRepository.php';

/**
 * A data repository connected to an imap server.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class ImapMessageFlagRepository extends ImapRepository
    implements \Conjoon\Data\Repository\Mail\MessageFlagRepository {

    /**
     * @var \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity
     */
    protected $account;

    /**
     * @var string
     */
    protected $imapConnectionClassName =
        '\Conjoon\Data\Repository\Remote\DefaultImapConnection';

    /**
     * @var string
     */
    protected $imapAdapteeClassName =
        '\Conjoon\Data\Repository\Remote\DefaultImapAdaptee';


    /**
     * @inheritdoc
     */
    protected function createConnectionForAccount(
        \Conjoon\Data\Entity\Mail\MailAccountEntity $mailAccount
    )
    {
        $connection = new $this->imapConnectionClassName(array(
            'imapAdaptee' => new $this->imapAdapteeClassName()
        ));

        $ssl = $mailAccount->getInboxConnectionType() == 'SSL'
               ? 'SSL'
               : ($mailAccount->getInboxConnectionType() == 'TLS'
                  ? 'TLS'
                  : false);


        $connection->connect(array(
            'host'     => $mailAccount->getServerInbox(),
            'port'     => $mailAccount->getPortInbox(),
            'user'     => $mailAccount->getUsernameInbox(),
            'password' => $mailAccount->getPasswordInbox(),
            'ssl'      => $ssl
        ));

        return $connection;
    }

    /**
     * Creates a new instance of the ImapMessageFlagRepository.
     *
     * @param \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity $account
     *
     */
    public function __construct(
        \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity $account
    )
    {
        $this->account = $account;
    }


    /**
     * @inheritdoc
     */
    public function setFlagsForUser(
            \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $folderFlagCollection,
            \Conjoon\User\User $user)
    {
        $connection = $this->getConnection(array('mailAccount' => $this->account));

        $connection->selectFolder($folderFlagCollection->getFolder());

        $flagCollection = $folderFlagCollection->getFlagCollection();

        $connection->setFlags($flagCollection);
    }



    /**
     * @inheritdoc
     */
    public function remove(\Conjoon\Data\Entity\DataEntity $entity)
    {
        throw new \RuntimeException("Not yet supported.");
    }

    /**
     * @inheritdoc
     */
    public function persist(\Conjoon\Data\Entity\DataEntity $entity)
    {
        throw new \RuntimeException("Not yet supported.");
    }

    /**
     * @inheritdoc
     */
    public function findById($id)
    {
        throw new \RuntimeException("Not yet supported.");
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        throw new \RuntimeException("Not yet supported.");
    }


    /**
     * @return string
     */
    public static function getEntityClassName()
    {
        return '\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity';
    }

}