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
        '\Conjoon\Data\EntityCreator\Mail\DefaultMessageAttachmentEntityCreator';

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
     *              - imapMessageAttachmentEntityCreatorClassName: a class name pointing to
     *                an implementation of
     *                \Conjoon\Data\EntityCreator\Mail\ImapMessageAttachmentEntityCreator.
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
            'imapMessageAttachmentEntityCreatorClassName' => array(
                'type'       => 'string',
                'allowEmpty' => false,
                'mandatory'  => false
            )
        ), $options);

        $options['imapMessageAttachmentEntityCreatorClassName'] =
            isset($options['imapMessageAttachmentEntityCreatorClassName'])
            ? $options['imapMessageAttachmentEntityCreatorClassName']
            : $this->entityCreatorClassName;

        $className = $options['imapMessageAttachmentEntityCreatorClassName'];
        $this->classLoader->loadClass($className);

        $this->entityCreator = new $className;

        if (!($this->entityCreator instanceof
            \Conjoon\Data\EntityCreator\Mail\MessageAttachmentEntityCreator)) {
            throw new InvalidArgumentException(
                "entity creator must be of type \"MessageAttachmentEntityCreator\""
            );
        }

    }

    /**
     * @inheritdoc
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
        return '\Conjoon\Data\Entity\Mail\DefaultMessageAttachmentEntity';
    }

}