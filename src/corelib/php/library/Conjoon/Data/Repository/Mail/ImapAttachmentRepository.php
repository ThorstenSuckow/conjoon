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

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException,
    Conjoon\Data\Repository\Mail\MailRepositoryException;

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * @see \Conjoon\Data\Repository\Mail\MailRepositoryException
 */
require_once 'Conjoon/Data/Repository/Mail/MailRepositoryException.php';

/**
 * @see \Conjoon\Data\Repository\Mail\MessageRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MessageRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\DefaultImapRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DefaultImapRepository.php';

/**
 * @see \Conjoon\Mail\Message\DefaultRawMessage
 */
require_once 'Conjoon/Mail/Message/DefaultRawMessage.php';

/**
 * A data repository connected to an imap server.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class ImapAttachmentRepository extends DefaultImapRepository
    implements \Conjoon\Data\Repository\Mail\AttachmentRepository {

    /**
     * @var string
     */
    protected $entityCreatorClassName =
        '\Conjoon\Data\EntityCreator\Mail\DefaultAttachmentEntityCreator';

    /**
     * @var string
     */
    protected $entityCreator;

    /**
     * Creates a new instance of this class.
     *
     * @param \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity $account
     * @param array $options Addiotonal set of options this repository gets
     *             configured with
     *              - imapAttachmentEntityCreatorClassName: a class name pointing to
     *                an implementation of
     *                \Conjoon\Data\EntityCreator\Mail\ImapAttachmentEntityCreator.
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws \Conjoon\Data\Repository\Mail\MailRepositoryException
     */
    public function __construct(
        \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity $account,
        array $options = array()
    )
    {
        parent::__construct($account, $options);

        $this->account = $account;

        ArgumentCheck::check(array(
            'imapAttachmentEntityCreatorClassName' => array(
                'type'       => 'string',
                'allowEmpty' => false,
                'mandatory'  => false
            )
        ), $options);

        $options['imapAttachmentEntityCreatorClassName'] =
            isset($options['imapAttachmentEntityCreatorClassName'])
            ? $options['imapAttachmentEntityCreatorClassName']
            : $this->entityCreatorClassName;

        $className = $options['imapAttachmentEntityCreatorClassName'];
        $this->classLoader->loadClass($className);

        $this->entityCreator = new $className;

        if (!($this->entityCreator instanceof
            \Conjoon\Data\EntityCreator\Mail\AttachmentEntityCreator)) {
            throw new InvalidArgumentException(
                "entity creator must be of type \"AttachmentEntityCreator\""
            );
        }

    }

    /**
     * Returns an entity identified by the passed $id. The $id
     * in this case must be of type \Conjoon\Mail\Client\Message\AttachmentLocation
     * representing the location of the attachment.
     *
     * @param {mixed} $id
     *
     * @return \Conjoon\Data\Entity\DataEntity
     *
     * @throws \Conjoon\Argument\InvalidArgumentException,
     *         \Conjoon\Data\Repository\Mail\MailRepositoryException
     */
    public function findById($id)
    {
        $data = array('attachmentLocation' => $id);

        ArgumentCheck::check(array(
            'attachmentLocation' => array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\Mail\Client\Message\AttachmentLocation'
            )
        ), $data);

        $attachmentLocation = $data['attachmentLocation'];

        $messageLocation = $attachmentLocation->getMessageLocation();

        $connection = $this->getConnection(array('mailAccount' => $this->account));

        $connection->selectFolder($messageLocation->getFolder());

        $message = $connection->getMessage($messageLocation->getUId());

        if ($message == null) {
            return null;
        }

        $raw = new \Conjoon\Mail\Message\DefaultRawMessage(
            $message['header'], $message['body']
        );

        try {
            $entities = $this->entityCreator->createListFrom($raw);
        } catch (\Conjoon\Data\EntityCreator\Mail\MailEntityCreatorException $e) {
            throw new MailRepositoryException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

        for ($i = 0, $len = count($entities); $i < $len; $i++) {
            if ($entities[$i]->getKey() == $attachmentLocation->getIdentifier()) {
                return $entities[$i];
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public static function getEntityClassName()
    {
        return '\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity';
    }

}
