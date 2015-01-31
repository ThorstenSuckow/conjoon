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

use Conjoon\Data\Repository\Remote\ImapConnectionException;

/**
 * @see \Conjoon\Data\Repository\Remote\AbstractImapAdaptee
 */
require_once 'Conjoon/Data/Repository/Remote/AbstractImapAdaptee.php';

/**
 * @see \Conjoon\Data\Repository\Remote\ImapConnectionException
 */
require_once 'Conjoon/Data/Repository/Remote/ImapConnectionException.php';

/**
 * An abstract base class for Imap Adaptees.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultImapAdaptee extends AbstractImapAdaptee  {

    /**
     * @var \Conjoon_Mail_Protocol_Imap
     */
    protected $protocol;

    /**
     * @var \Conjoon_Mail_Storage_Imap
     */
    protected $storage;

    /**
     * @inheritdoc
     */
    protected function establishConnection($host, $port, $user, $password, $ssl = false)
    {
        /**
         * @see \Conjoon_Mail_Protocol_Imap
         */
        require_once 'Conjoon/Mail/Protocol/Imap.php';

        $protocol = new \Conjoon_Mail_Protocol_Imap();
        try {
            $protocol->connect($host, $port, $ssl);
        } catch (\Exception $e) {
            throw new ImapConnectionException(
                "Exception thrown by previous exception: "
                . $e->getMessage(), 0, $e
            );
        }

        if (!$protocol->login($user, $password)) {
            throw new ImapConnectionException(
                'cannot login, user or password wrong'
            );
        }

        $this->protocol = $protocol;

        /**
         *@see \Conjoon_Mail_Storage_Imap
         */
        require_once 'Conjoon/Mail/Storage/Imap.php';

        $this->storage = new \Conjoon_Mail_Storage_Imap($this->protocol);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function addFlagToMessage($flag, $id)
    {
        return $this->setFlagForMessage($flag, $id, '+');
    }

    /**
     * @inheritdoc
     */
    protected function removeFlagFromMessage($flag, $id)
    {
        return $this->setFlagForMessage($flag, $id, '-');
    }



    /**
     * @inheritdoc
     */
    protected function _selectFolder($path)
    {
        try {
            $this->storage->selectFolder($path);
        } catch (\Exception $e) {
            throw new ImapConnectionException(
                "Exception thrown by previous exception: "
                . $e->getMessage(), 0, $e
            );
        }

        return $this->storage->getCurrentFolder();
    }

    /**
     * @inheritdoc
     */
    protected function _getFolderDelimiter()
    {
        try {
            $mailboxes = $this->protocol->listMailbox('', 'INBOX');
        } catch (\Exception $e) {
            throw new ImapConnectionException(
                "Exception thrown by previous exception: "
                . $e->getMessage(), 0, $e
            );
        }

        $delim = "";

        foreach ($mailboxes as $globalName => $data) {
            if (isset($data['delim'])) {
                $delim = $data['delim'];
                break;
            }
        }

        if ($delim == "") {
            throw new ImapConnectionException(
                "No delimiter found."
            );
        }

        return $delim;

    }

    /**
     * @inheritdoc
     */
    protected function _getMessage($uId)
    {
        try {
            $num = $this->storage->getNumberByUniqueId($uId);

            $result = $this->protocol->fetch(
                array('RFC822.HEADER', 'RFC822.TEXT'), $num
            );

            return array(
                'header' => $result['RFC822.HEADER'],
                'body'   => $result['RFC822.TEXT']
            );

        } catch (\Exception $e) {
            throw new ImapConnectionException(
                "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function disconnect()
    {
        $this->protocol->logout();
        $this->protocol = null;
        $this->storage  = null;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function isConnected()
    {
        return $this->protocol != null;
    }

// -------- helper

    /**
     * Internal helper function for message flags.
     *
     * @return bool
     *
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    protected function setFlagForMessage($flag, $id, $mode)
    {
        try {
            $num = $this->storage->getNumberByUniqueId($id);
            return $this->protocol->store(array($flag), $num, null, $mode, true);
        } catch (\Exception $e) {
            throw new ImapConnectionException(
                "Exception thrown by previous exception: "
                . $e->getMessage(), 0, $e
            );
        }

    }


}