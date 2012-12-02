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
class DefaultProtocol implements Protocol{

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
    public function setFlags(
        \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $flagCollection,
        \Conjoon\User\User $user)
    {
        $result = null;

        try {
            $result = $this->adaptee->setFlags($flagCollection, $user);
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