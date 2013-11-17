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

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException,
    Conjoon\Data\Repository\Mail\MailRepositoryException;

/**
 * A data repository connected to an imap server.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class ImapMessageRepository extends DefaultImapRepository
    implements \Conjoon\Data\Repository\Mail\MessageRepository {

    /**
     * @var string
     */
    protected $entityCreatorClassName =
        '\Conjoon\Data\EntityCreator\Mail\DefaultImapMessageEntityCreator';

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
     *              - imapMessageEntityCreatorClassName: a class name pointing to
     *                an implementation of
     *                \Conjoon\Data\EntityCreator\Mail\ImapMessageEntityCreator.
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
            'imapMessageEntityCreatorClassName' => array(
                'type'       => 'string',
                'allowEmpty' => false,
                'mandatory'  => false
            )
        ), $options);

        $options['imapMessageEntityCreatorClassName'] =
            isset($options['imapMessageEntityCreatorClassName'])
            ? $options['imapMessageEntityCreatorClassName']
            : $this->entityCreatorClassName;

        $className = $options['imapMessageEntityCreatorClassName'];
        $this->classLoader->loadClass($className);

        $this->entityCreator = new $className;

        if (!($this->entityCreator instanceof
            \Conjoon\Data\EntityCreator\Mail\ImapMessageEntityCreator)) {
            throw new InvalidArgumentException(
                "entity creator must be of type \"ImapMessageEntityCreator\""
            );
        }

    }

    /**
     * @inheritdoc
     */
    public function findById($id)
    {
        $data = array('messageLocation' => $id);

        ArgumentCheck::check(array(
            'messageLocation' => array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\Mail\Client\Message\MessageLocation'
            )
        ), $data);

        $messageLocation = $data['messageLocation'];

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
            $entity = $this->entityCreator->createFrom($raw);
        } catch (\Conjoon\Data\EntityCreator\Mail\MailEntityCreatorException $e) {
            throw new MailRepositoryException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

        // update id of the imap message here by using reflection class
        // this is needed since each MessageEntity needs to return an actual
        // value for getId()
        $reflectionClass = new \ReflectionClass(get_class($entity));
        $property = $reflectionClass->getProperty("id");
        $property->setAccessible(true);
        $property->setValue($entity, $messageLocation->getUId());
        $property->setAccessible(false);


        return $entity;
    }

    /**
     * @return string
     */
    public static function getEntityClassName()
    {
        return '\Conjoon\Data\Entity\Mail\ImapMessageEntity';
    }

}
