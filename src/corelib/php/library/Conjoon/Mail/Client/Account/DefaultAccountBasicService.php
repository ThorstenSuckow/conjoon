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


namespace Conjoon\Mail\Client\Account;

use Conjoon\Mail\Client\Account\AccountServiceException,
    Conjoon\Mail\Client\Account\AccountDoesNotExistException,
    Conjoon\Argument\ArgumentCheck;

/**
 * @see Conjoon\Mail\Client\Account\AccountBasicService
 */
require_once 'Conjoon/Mail/Client/Account/AccountBasicService.php';

/**
 * @see Conjoon\Mail\Client\Account\AccountServiceException
 */
require_once 'Conjoon/Mail/Client/Account/AccountServiceException.php';

/**
 * @see Conjoon\Mail\Client\Account\AccountDoesNotExistException
 */
require_once 'Conjoon/Mail/Client/Account/AccountDoesNotExistException.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * Default implementation for AccountBasicService.
 *
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAccountBasicService implements AccountBasicService {

    /**
     * @type \Conjoon\Data\Repository\Mail\MailAccountRepository
     */
    protected $mailAccountRepository;

    /**
     * Creates a new instance of DefaultAccountBasicService.
     *
     * @param Array $options an array with the following key/value-pairs:
     *
     */
    public function __construct(Array $options)
    {
        $data = array('options' => $options);

        ArgumentCheck::check(array(
            'options' => array(
                'type'       => 'array',
                'allowEmpty' => false
            )
        ), $data);

        ArgumentCheck::check(array(
            'mailAccountRepository' => array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\Data\Repository\Mail\MailAccountRepository'
            )
        ), $options);

        $this->mailAccountRepository = $options['mailAccountRepository'];
    }

    /**
     * @inheritdoc
     */
    public function getAccountEntity(
        \Conjoon\Mail\Client\Account\Account $account) {

        $id = $account->getId();

        try {
            $orgEntity = $this->mailAccountRepository->findById($id);
        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            throw new AccountServiceException(
                "Exception thrown by previous exception", 0, $e
            );
        }

        if (!$orgEntity) {
            throw new AccountDoesNotExistException(
                "Account $account was not found"
            );
        }

        return $orgEntity;
    }

}