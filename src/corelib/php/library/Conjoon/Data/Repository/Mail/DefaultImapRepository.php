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


namespace Conjoon\Data\Repository\Mail;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see Conjoon\Data\Repository\Mail\ImapRepository
 */
require_once 'Conjoon/Data/Repository/Mail/ImapRepository.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class DefaultImapRepository extends ImapRepository {

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
     * Creates a new instance of this class.
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
        }


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
    public function remove(\Conjoon\Data\Entity\DataEntity $entity)
    {
        throw new \RuntimeException("Not yet supported.");
    }

    /**
     * @inheritdoc
     */
    public function register(\Conjoon\Data\Entity\DataEntity $entity)
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

}
