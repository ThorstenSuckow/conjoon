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

namespace Conjoon\Mail\Client\Account;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see Conjoon\Mail\Client\Account\AccountService
 */
require_once 'Conjoon/Mail/Client/Account/AccountService.php';

/**
 * @see Conjoon\Mail\Client\Account\AccountServiceException
 */
require_once 'Conjoon/Mail/Client/Account/AccountServiceException.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * Default implementation of AccountService
 *
 * @category   Conjoon_Mail
 * @package    Account
 *
 * @uses AccountService
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAccountService implements AccountService {

    /**
     * @var \Conjoon\User\User
     */
    protected $user;

    /**
     * @var \Conjoon\Data\Repository\Mail\MailFolderRepository
     */
    protected $folderRepository;

    /**
     * @var \Conjoon\Mail\Client\Folder\FolderService
     */
    protected $folderService;

    /**
     * @var \Conjoon\Data\Repository\Data\Mail\MailAccountRepository
     */
    protected $mailAccountRepository;

    /**
     * Creates a new instance of an account service.
     * An account service is bound to a user.
     *
     * @param array $options An array with instances of MailFolderRepository,
     *                       and a User to use.
     *                       - user: and instance of \Conjoon\User\User
     *                       - folderService: an instance of
     *                         Conjoon\Mail\Client\Folder\FolderService
     *                       - mailAccountRepository: an instance of
     *                         \Conjoon\Data\Repository\Mail\MailAccountRepository
     *
     */
    public function __construct(Array $options)
    {
        ArgumentCheck::check(array(
            'user' => array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\User\User'
            ),
            'folderService' => array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\Mail\Client\Folder\FolderService'
            ),
            'mailAccountRepository' => array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\Data\Repository\Mail\MailAccountRepository'
            )
        ), $options);

        $this->user                  = $options['user'];
        $this->folderService         = $options['folderService'];
        $this->mailAccountRepository = $options['mailAccountRepository'];
    }

    /**
     * @inheritdoc
     */
    public function getMailAccountToAccessRemoteFolder(
        \Conjoon\Mail\Client\Folder\Folder $folder)
    {
        try {
            $isRemote =
                $this->folderService->isFolderRepresentingRemoteMailbox($folder);
        } catch (\Exception $e) {

            throw new \Conjoon\Mail\Client\Account\AccountServiceException(
                "Exception thrown by previous exception: "
                    . $e->getMessage(), 0, $e
            );

        }

        if (!$isRemote) {
            throw new \Conjoon\Mail\Client\Account\AccountServiceException(
                "Folder seems to be a local instead of a remote folder"
            );
        }

        try{
            $entity = $this->folderService->getFolderEntity(
                new \Conjoon\Mail\Client\Folder\Folder(
                    new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                        '["root", "' . $folder->getRootId() . '"]'
                    )
                )
            );
        } catch (\Exception $e) {

            throw new \Conjoon\Mail\Client\Account\AccountServiceException(
                "Exception thrown by previous exception: "
                 . $e->getMessage(), 0, $e
            );
        }

        if ($entity === null) {
            throw new \Conjoon\Mail\Client\Account\AccountServiceException(
                "Client folder with id " . $folder->getRootId() . " was not found"
            );
        }

        $accounts = $entity->getMailAccounts();

        if (count($accounts) > 1) {
            throw new \Conjoon\Mail\Client\Account\AccountServiceException(
                "Unexpected multiple accounts returned for folder. "
                . "No remote folder?"
            );
        }

        $account = $accounts[0];

        return $account;
    }

    /**
     * @inheritdoc
     */
    public function getStandardMailAccount()
    {
        return $this->mailAccountRepository->getStandardMailAccount(
            $this->user);
    }

    /**
     * @inheritdoc
     */
    public function getMailAccounts()
    {
        return $this->mailAccountRepository->getMailAccounts(
            $this->user);
    }

    /**
     * @inheritdoc
     */
    public function getMailAccountForMailAddress($address) {

        $data = array('address' => $address);

        try{
            ArgumentCheck::check(array(
                'address' => array(
                    'type'       => 'string',
                    'allowEmpty' => false,
                    'strict'     => true
                )), $data);
        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            throw new \Conjoon\Mail\Client\Account\AccountServiceException(
                "mail address \"$address\" does not seem to be a valid " .
                "mail address.", 0, $e
            );
        }

        $address = $data['address'];
        $accounts = $this->getMailAccounts();

        foreach ($accounts as $account) {
            if (strtolower($account->getAddress()) === $address ||
                strtolower($account->getReplyAddress()) === $address) {
                return $account;
            }
        }

        return null;
    }

}