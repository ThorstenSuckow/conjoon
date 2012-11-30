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


namespace Conjoon\Mail\Client\Folder;

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException;

/**
 * @see MailFolderCommons
 */
require_once 'Conjoon/Mail/Client/Folder/MailFolderCommons.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * A default implementation for MailFolderCommons.
 *
 * @category   Conjoon_Mail
 * @package    Folder
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailFolderCommons implements MailFolderCommons {

    /**
     * @var DoctrineMailFolderRepository
     */
    protected $folderRepository;

    /**
     * @var Conjoon\User\User
     */
    protected $user;

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
            )
        ), $options);

        $this->folderRepository = $options['mailFolderRepository'];
        $this->user             = $options['user'];
    }

    /**
     * @inheritdoc
     */
    public function doesMailFolderExist(MailFolder $folder)
    {
        $path = $folder->getPath();

        if (!empty($path)) {
            $id = array_pop($path);

            $entity = $this->folderRepository->find($id);

            while (!empty($path)) {

                $id = array_pop($path);

                $entity = $entity->getParent();

                if ($entity === null || $entity->getId() != $id) {
                    return false;
                }
            }

            $entity = $entity->getParent();
        } else {
            $entity = $this->folderRepository->find($folder->getRootId());
        }

        return $entity !== null && $folder->getRootId() == $entity->getId();
    }

}