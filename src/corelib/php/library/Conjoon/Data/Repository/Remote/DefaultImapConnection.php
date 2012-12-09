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


namespace Conjoon\Data\Repository\Remote;

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException,
    Conjoon\Data\Repository\Remote\ImapConnectionException;

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see \Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * @see \Conjoon\Data\Repository\Remote\ImapConnection;
 */
require_once 'Conjoon/Data/Repository/Remote/ImapConnection.php';

/**
 * @see \Conjoon\Data\Repository\Remote\ImapConnectionException;
 */
require_once 'Conjoon/Data/Repository/Remote/ImapConnectionException.php';

/**
 * @see \Conjoon\Mail\Client\Message\Flag\JunkFlag
 */
require_once 'Conjoon/Mail/Client/Message/Flag/JunkFlag.php';

/**
 * @see \Conjoon\Mail\Client\Message\Flag\NotJunkFlag
 */
require_once 'Conjoon/Mail/Client/Message/Flag/NotJunkFlag.php';

/**
 * A default implementation of an Imap Connection.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultImapConnection implements ImapConnection {

    /**
     * @var \Conjoon\Data\Repository\Remote\ImapAdaptee
     */
    protected $imapAdaptee;

    /**
     * Creates a new instance of this class.
     *
     * @param array $options An array of options this object should be
     *              configured with.
     *              - imapAdaptee: An instance of
     *              \Conjoon\Data\Repository\Remote\ImapAdaptee
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(array $options)
    {
        ArgumentCheck::check(array(
            'imapAdaptee' => array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\Data\Repository\Remote\ImapAdaptee'
            )
        ), $options);

        $this->imapAdaptee = $options['imapAdaptee'];

    }

    /**
     * @inheritdoc
     */
    public function connect(array $options)
    {
       return $this->imapAdaptee->connect($options);
    }


    /**
     * @inheritdoc
     */
    public function selectFolder(\Conjoon\Mail\Folder\FolderPath $path)
    {
        $folderDelimiter = $this->getFolderDelimiter();

        $path = implode($folderDelimiter, $path->getPath());

        return $this->imapAdaptee->selectFolder($path);
    }

    /**
     * @inheritdoc
     */
    public function setFlags(\Conjoon\Mail\Message\Flag\FlagCollection $flagCollection)
    {
        $collection = $flagCollection->getFlags();

        foreach ($collection as $messageFlag) {

            try {

                $removeFlag = null;

                // remove junk/notjunk since they re mutual exclusive
                switch (true) {
                    case ($messageFlag instanceof \Conjoon\Mail\Message\Flag\JunkFlag
                          && !$messageFlag->isClear()):
                        $removeFlag = new \Conjoon\Mail\Client\Message\Flag\NotJunkFlag(
                            $messageFlag->getMessageId(), true
                        );
                    break;

                    case ($messageFlag instanceof \Conjoon\Mail\Message\Flag\NotJunkFlag
                          && !$messageFlag->isClear()):
                        $removeFlag = new \Conjoon\Mail\Client\Message\Flag\JunkFlag(
                            $messageFlag->getMessageId(), true
                        );
                        break;
                }

                if ($removeFlag) {
                    $this->imapAdaptee->setFlag(
                        $removeFlag->__toString(),
                        $removeFlag->getMessageId(),
                        '-'
                    );
                }

                $this->imapAdaptee->setFlag(
                    $messageFlag->__toString(),
                    $messageFlag->getMessageId(),
                    ($messageFlag->isClear() ? '-' : '+')
                );
            } catch (InvalidArgumentException $e) {
                throw new ImapConnectionException(
                    "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
                );
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getFolderDelimiter()
    {
       return $this->imapAdaptee->getFolderDelimiter();
    }

    /**
     * @inheritdoc
     */
    public function disconnect()
    {
        return $this->imapAdaptee->disconnect();
    }

    /**
     * @inehritdoc
     */
    public function isConnected()
    {
        return $this->imapAdaptee->isConnected();
    }


}