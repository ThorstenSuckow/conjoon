<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
abstract class AbstractImapAdaptee implements ImapAdaptee  {

    /**
     * Establishes the connection using the specified connection informations.
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param bool $ssl
     *
     * @return boolean true if the connection was established
     *
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    abstract protected function establishConnection(
        $host, $port, $user, $password, $ssl = false);

    /**
     * Returns the message for the specified id.
     *
     * @param string $messageId
     *
     * @return array|null
     *
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    abstract protected function _getMessage($uId);

    /**
     * Sets the flag for the specified message.
     *
     * @param string $flag
     * @param mixed $id
     *
     * @return boolean
     *
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    abstract protected function addFlagToMessage($flag, $id);

    /**
     * Removes the flag for the specified message.
     *
     * @param string $flag
     * @param mixed $id
     *
     * @return boolean
     *
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    abstract protected function removeFlagFromMessage($flag, $id);

    /**
     * Selects the folder for the specified path.
     *
     * @param string $path
     * @return boolean
     *
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    abstract protected function _selectFolder($path);

    /**
     * Return sthe folder delimiter.
     *
     * @return string
     *
     * @throws \Conjoon\Data\Repository\Remote\ImapConnectionException
     */
    abstract protected function _getFolderDelimiter();

    /**
     * @inheritdoc
     */
    public function connect(array $options)
    {
        if ($this->isConnected()) {
            throw new ImapConnectionException(
                "Connection already established"
            );
        }

        $stringCheck = array(
            'type'       => 'string',
            'allowEmpty' => false
        );

        ArgumentCheck::check(array(
            'user'     => $stringCheck,
            'host'     => $stringCheck,
            'password' => $stringCheck,
            'port'     => array(
                'type'        => 'int',
                'allowEmpty'  => false,
                'greaterThan' => 1
            )
        ), $options);

        $ssl = false;
        if (isset($options['ssl'])) {
            if ($options['ssl'] != 'SSL'
                && $options['ssl'] != 'TLS'
                && $options['ssl'] !== false) {
                throw new InvalidArgumentException(
                    "Invalid connection option for \"ssl\": " . $options['ssl']
                );
            }

            $ssl = $options['ssl'];
        }

        return $this->establishConnection(
            $options['host'], $options['port'], $options['user'],
            $options['password'], $ssl
        );
    }

    /**
     * @inheritdoc
     */
    public function setFlag($flag, $id, $mode)
    {
        $this->throwExceptionIfNotConnected();

        if ($mode !== '-' && $mode !== '+') {
            throw new InvalidArgumentException("Invalid mode \"$mode\"");
        }
        if ($mode === '-') {
            return $this->removeFlagFromMessage($flag, $id);
        }

        if ($mode === '+') {
            return $this->addFlagToMessage($flag, $id);
        }
    }

    /**
     * @inheritdoc
     */
    public function getMessage($uId)
    {
        $this->throwExceptionIfNotConnected();

        $data = array('uId' => $uId);

        ArgumentCheck::check(array(
            'uId' => array(
                'type'        => 'string',
                'allowEmpty'  => false
            )
        ), $data);

        $uId = $data['uId'];

        return $this->_getMessage($uId);

    }

    /**
     * @inheritdoc
     */
    public function selectFolder($path)
    {
        $this->throwExceptionIfNotConnected();

        return $this->_selectFolder($path);
    }

    /**
     * @inheritdoc
     */
    public function getFolderDelimiter()
    {
        $this->throwExceptionIfNotConnected();

        return $this->_getFolderDelimiter();
    }

    /**
     * Throws an expception if no connection is available.
     *
     * @throws ImapConnectionException
     */
    protected function throwExceptionIfNotConnected()
    {
        if (!$this->isConnected()) {
            throw new ImapConnectionException(
                "No connection available."
            );
        }
    }

}