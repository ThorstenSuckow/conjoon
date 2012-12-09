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
     * Creates a new instance of an account service.
     * An account service is bound to a user.
     *
     * @param array $options An array with instances of MailFolderRepository,
     *                       and a User to use.
     *                       - user: and instance of \Conjoon\User\User
     *                       - folderService: an instance of
     *                         Conjoon\Mail\Client\Folder\FolderService
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
            )
        ), $options);

        $this->user              = $options['user'];
        $this->folderService     = $options['folderService'];
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


}