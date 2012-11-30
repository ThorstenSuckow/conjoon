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

namespace Conjoon\Mail\Client\Security;

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException;

/**
 * @see MailFolderSecurityService
 */
require_once 'Conjoon/Mail/Client/Security/MailFolderSecurityService.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * @see Conjoon\Mail\Client\Security\SecurityServiceException
 */
require_once 'Conjoon/Mail/Client/Security/SecurityServiceException.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers
 */
require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersUsers.php';

/**
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailFolderSecurityService implements MailFolderSecurityService {

    /**
     * @var DoctrineMailFolderRepository
     */
    protected $folderRepository;

    /**
     * @var Conjoon\User\User
     */
    protected $user;

    /**
     * @var Conjoon\Mail\Client\Folder\MailFolderCommons
     */
    protected $folderCommons;


    /**
     * @inheritdoc
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
            'mailFolderRepository' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon\Data\Repository\Mail\MailFolderRepository'
            ),
            'user' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon\User\User'
            ),
            'mailFolderCommons' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon\Mail\Client\Folder\MailFolderCommons'
            ),
        ), $options);

        $this->folderRepository = $options['mailFolderRepository'];
        $this->user             = $options['user'];
        $this->folderCommons    = $options['mailFolderCommons'];
    }

    /**
     * @inheritdoc
     */
    public function isMailFolderAccessible(
        \Conjoon\Mail\Client\Folder\MailFolder $folder)
    {
        /**
         * @refactor uses old implementation
         */

        $path   = $folder->getPath();
        $nodeId = $folder->getNodeId();
        $rootId = $folder->getRootId();

        $checkNodeId = null;

        switch (true) {

            // only root id available, check only root
            case (empty($path) && empty($nodeId)):

                $checkNodeId = $rootId;

                break;

            // paths set, node id avilable.
            case (!empty($path) && !empty($nodeId)):

                try {
                    $doesMailFolderExist =
                        $this->folderCommons->doesMailFolderExist($folder);
                } catch (\Conjoon\Mail\Client\Folder\ClientMailFolderServiceException $e) {
                    throw new SecurityServiceException(
                        "Exception thrown by previous exception: "
                        . $e->getMessage, 0, $e
                    );
                }

                // check if node id exists client side
                if ($doesMailFolderExist) {
                    // check if node is accessible
                    $checkNodeId = $nodeId;

                } else {
                    // check if root node is accessible
                    $checkNodeId = $rootId;
                }

                break;

            default:
                throw new SecurityServiceException(
                    "Could not check whether folder \""
                        . $folder->__toString()
                        . "\" is accessible "
                );

        }

        $foldersUsers =
            new \Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers();

        $rel = $foldersUsers->getRelationShipForFolderAndUser(
            $checkNodeId, $this->user->getId()
        );

        return $rel ===
            \Conjoon_Modules_Groupware_Email_Folder_Model_FoldersUsers::OWNER;


    }


}