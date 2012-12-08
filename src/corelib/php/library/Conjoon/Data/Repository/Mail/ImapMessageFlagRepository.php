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

use Conjoon\Argument\InvalidArgumentException,
    Conjoon\Argument\ArgumentCheck,
    Conjoon\Data\Repository\Mail\MailRepositoryException;

/**
 * @see \ Conjoon\Data\Repository\Mail\MailRepositoryException
 */
require_once 'Conjoon/Data/Repository/Mail/MailRepositoryException.php';


/**
 * @see \Conjoon\Data\Repository\Mail\MessageFlagRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MessageFlagRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\ImapRepository
 */
require_once 'Conjoon/Data/Repository/Mail/ImapRepository.php';

/**
 * @see \Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

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
     * @var \Conjoon\Lang\ClassLoader
     */
    protected $classLoader;

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
     * Creates a new instance of the ImapMessageFlagRepository.
     *
     * @param \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity $account
     * @param array $options An array of configuration options, optional,
     *              with the following key/value pairs:
     *              - classLoader: an instance of \Conjoon\Lang\ClassLoader. If
     *                omitted, Conjoon\Lang\DefaultClassLoader will be used.
     *              - imapConnectionClassName: the name of the class to use
     *                for the imap repository connection, must inherit from
     *                \Conjoon\Data\Repository\Remote\ImapConnection
     *              - imapAdapteeClassName: the adaptee to use for the
     *                imapConnection, must inherit from
     *                \Conjoon\Data\Repository\Remote\ImapAdaptee
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws \Conjoon\Data\Repository\Mail\MailRepositoryException
     */
    public function __construct(
        \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity $account,
        array $options = array()
    )
    {
        $this->account = $account;

        if (!empty($options)) {

            ArgumentCheck::check(array(
                'classLoader' => array(
                    'type'       => 'instanceof',
                    'class'      => '\Conjoon\Lang\ClassLoader',
                    'allowEmpty' => false,
                    'mandatory'  => false
                ),
                'imapConnectionClassName' => array(
                    'type'       => 'string',
                    'allowEmpty' => false,
                    'mandatory'  => false
                ),
                'imapAdapteeClassName' => array(
                    'type'       => 'string',
                    'allowEmpty' => false,
                    'mandatory'  => false
                )
            ), $options);

            if (isset($options['classLoader'])) {
                $this->classLoader = $options['classLoader'];
            } else {
                /**
                 * @see \Conjoon\Lang\DefaultClassLoader
                 */
                require_once 'Conjoon/Lang/DefaultClassLoader.php';

                $this->classLoader = new \Conjoon\Lang\DefaultClassLoader();
            }

            try {
                if (isset($options['imapConnectionClassName'])) {
                    $this->imapConnectionClassName = $options['imapConnectionClassName'];
                    $this->classLoader->loadClass($this->imapConnectionClassName);
                }

                if (isset($options['imapAdapteeClassName'])) {
                    $this->imapAdapteeClassName = $options['imapAdapteeClassName'];
                    $this->classLoader->loadClass($this->imapAdapteeClassName);
                }
            } catch (\Exception $e) {
                throw new MailRepositoryException(
                    "Exception thrown by previous exception: "
                    . $e->getMessage(),0, $e
                );
            }
        }

    }

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
     * @inheritdoc
     */
    public function setFlagsForUser(
            \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $folderFlagCollection,
            \Conjoon\User\User $user)
    {
        $connection = $this->getConnection(array('mailAccount' => $this->account));

        $connection->selectFolder($folderFlagCollection->getFolder());

        $flagCollection = $folderFlagCollection->getFlagCollection();

        return $connection->setFlags($flagCollection);
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