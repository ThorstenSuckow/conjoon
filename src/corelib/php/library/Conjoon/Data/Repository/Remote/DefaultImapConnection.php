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
                            $messageFlag->getUId(), true
                        );
                    break;

                    case ($messageFlag instanceof \Conjoon\Mail\Message\Flag\NotJunkFlag
                          && !$messageFlag->isClear()):
                        $removeFlag = new \Conjoon\Mail\Client\Message\Flag\JunkFlag(
                            $messageFlag->getUId(), true
                        );
                        break;
                }

                if ($removeFlag) {
                    $this->imapAdaptee->setFlag(
                        $removeFlag->__toString(),
                        $removeFlag->getUId(),
                        '-'
                    );
                }

                $this->imapAdaptee->setFlag(
                    $messageFlag->__toString(),
                    $messageFlag->getUId(),
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
    public function getMessage($uId)
    {
        try {
            return $this->imapAdaptee->getMessage($uId);
        } catch (InvalidArgumentException $e) {
            throw new ImapConnectionException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }
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