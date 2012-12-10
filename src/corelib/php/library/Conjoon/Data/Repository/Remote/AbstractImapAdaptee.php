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
    abstract protected function _getMessage($messageId);

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
    public function getMessage($messageId)
    {
        $this->throwExceptionIfNotConnected();

        $data = array('messageId' => $messageId);

        ArgumentCheck::check(array(
            'messageId' => array(
                'type'        => 'string',
                'allowEmpty'  => false
            )
        ), $data);

        $messageId = $data['messageId'];

        return $this->_getMessage($messageId);

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