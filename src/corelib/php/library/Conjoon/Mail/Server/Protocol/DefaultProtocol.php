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


namespace Conjoon\Mail\Server\Protocol;

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException;

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see \Conjoon\Mail\Server\Protocol\Protocol
 */
require_once 'Conjoon/Mail/Server/Protocol/Protocol.php';

/**
 * @see \Conjoon\Mail\Server\Protocol\DefaultResult\ErrorResult
 */
require_once 'Conjoon/Mail/Server/Protocol/DefaultResult/ErrorResult.php';

/**
 * A default implementation for a \Conjoon\Mail\Server\Protocol\Protocol.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultProtocol implements Protocol {

    /**
     * @var \Conjoon\Mail\Server\Protocol\ProtocolAdaptee
     */
    protected $adaptee;

    /**
     * Creates a new instance of this protocol.
     *
     * @param ProtocolAdaptee $adaptee The adaptee to use for this protocol.
     *
     */
    public function __construct(\Conjoon\Mail\Server\Protocol\ProtocolAdaptee $adaptee)
    {
        $this->adaptee = $adaptee;
    }


    /**
     * @inheritdoc
     */
    public function setFlags(array $options)
    {
        $result = null;

        try {

            ArgumentCheck::check(array(
                'user' => array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\User\User'
                ),
                'parameters' => array(
                    'type'       => 'array',
                    'allowEmpty' => false
                )
            ), $options);

            ArgumentCheck::check(array(
                'folderFlagCollection' => array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Mail\Client\Message\Flag\FolderFlagCollection'
                )
            ), $options['parameters']);

        } catch (InvalidArgumentException $e) {
            return $this->getResultForException($e);
        }

        try {
            $result = $this->adaptee->setFlags(
                $options['parameters']['folderFlagCollection'],
                $options['user']
            );
        } catch (ProtocolException $e) {
            $result = $this->getResultForException($e);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getMessage(array $options)
    {
        $result = null;

        try {

            ArgumentCheck::check(array(
                'user' => array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\User\User'
                ),
                'parameters' => array(
                    'type'       => 'array',
                    'allowEmpty' => false
                )
            ), $options);

            ArgumentCheck::check(array(
                'messageLocation' => array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Mail\Client\Message\MessageLocation'
                )
            ), $options['parameters']);

        } catch (InvalidArgumentException $e) {
            return $this->getResultForException($e);
        }

        try {
            $result = $this->adaptee->getMessage(
                $options['parameters']['messageLocation'],
                $options['user']
            );
        } catch (ProtocolException $e) {
            $result = $this->getResultForException($e);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getAttachment(array $options)
    {
        $result = null;

        try {

            ArgumentCheck::check(array(
                'user' => array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\User\User'
                ),
                'parameters' => array(
                    'type'       => 'array',
                    'allowEmpty' => false
                )
            ), $options);

            ArgumentCheck::check(array(
                'attachmentLocation' => array(
                    'type'  => 'instanceof',
                    'class' => '\Conjoon\Mail\Client\Message\AttachmentLocation'
                )
            ), $options['parameters']);

        } catch (InvalidArgumentException $e) {
            return $this->getResultForException($e);
        }

        try {
            $result = $this->adaptee->getAttachment(
                $options['parameters']['attachmentLocation'],
                $options['user']
            );
        } catch (ProtocolException $e) {
            $result = $this->getResultForException($e);
        }

        return $result;
    }


// -------- helper

    /**
     * Creates and returns a new instance of ErrorResult
     *
     * @return \Conjoon\Mail\Server\Protocol\DefaultResult\ErrorResult
     */
    public function getResultForException(\Exception $e)
    {
        return new \Conjoon\Mail\Server\Protocol\DefaultResult\ErrorResult($e);
    }


}