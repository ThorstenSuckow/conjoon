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


namespace Conjoon\Data\Repository\Mail;

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException;

/**
 * @see Conjoon\Data\Repository\RemoteRepository
 */
require_once 'Conjoon/Data/Repository/RemoteRepository.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class ImapRepository implements \Conjoon\Data\Repository\RemoteRepository {

    /**
     * @var array
     */
    protected $connectionPool;

    /**
     * Creates a connection for the specified account which will be put in
     * the connection pool for later reuse.
     *
     * @param \Conjoon\Data\Entity\Mail\MailAccountEntity $mailAccount
     *
     * @return \Conjoon\Data\Repository\Remote\RemoteConnection
     */
    protected abstract function createConnectionForAccount(
        \Conjoon\Data\Entity\Mail\MailAccountEntity $mailAccount
    );


    /**
     * Returns the connection object for this repository.
     * Clients must specify a mail account object with the options, holding the
     * connection information and user credentials for the remote repository.
     *
     * @param array $options an array An array containing configuration information
     *              for the connection which should be established
     *              - mailAccount: an instance of
     *              \Conjoon\Data\Entity\Mail\MailAccountEntity
     *
     * @return \Conjoon\Data\Repository\Remote\RemoteConnection
     *
     * @throws \Conjoon\Argument\InvalidArgumentException if the mail account
     * object was not specified, or if the account was invalid configured
     */
    public function getConnection(array $options = array())
    {
        ArgumentCheck::check(array(
            'mailAccount' => array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\Data\Entity\Mail\MailAccountEntity'
            )
        ), $options);

        $mailAccount = $options['mailAccount'];

        if (!$mailAccount->getId()) {
            throw new InvalidArgumentException(
                "The specified mail account does not contain a valid id"
            );
        }

         return $this->getConnectionFromPoolForAccount($mailAccount);
    }

    /**
     * Returns a pooled connection.
     *
     * @param  \Conjoon\Data\Entity\Mail\MailAccountEntity $mailAccount
     *
     * @return \Conjoon\Data\Repository\Remote\RemoteConnection
     */
    protected function getConnectionFromPoolForAccount(
            \Conjoon\Data\Entity\Mail\MailAccountEntity $mailAccount)
    {
        $id = $mailAccount->getId();

        if (!isset($this->connectionPool[$id])) {
            $this->connectionPool[$id] = $this->createConnectionForAccount($mailAccount);
        }

        return $this->connectionPool[$id];
    }


}