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


namespace Conjoon\Mail\Client\Message;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see \Conjoon\Mail\Client\Message\AttachmentLocation
 */
require_once 'Conjoon/Mail/Client/Message/AttachmentLocation.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * Default implementation for a MessageLocation
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAttachmentLocation implements AttachmentLocation {

    /**
     * @var Conjoon\Mail\Client\Message\MessageLocation
     */
    protected $messageLocation;

    /**
     * @var string
     */
    protected $id;

    /**
     * Creates a new instance of this class.
     *
     * @param \Conjoon\Mail\Client\Message\MessageLocation $messageLocation
     * @param mixed $id
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(MessageLocation $messageLocation, $id)
    {
        $data = array('id' => $id);

        ArgumentCheck::check(array(
            'id' => array(
                'type'       => 'string',
                'allowEmpty' => false
        )), $data);

        $id = $data['id'];

        $this->id = $id;

        $this->messageLocation = $messageLocation;
    }

    /**
     * @inheritdoc
     */
    public function getMessageLocation()
    {
        return $this->messageLocation;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier()
    {
        return $this->id;
    }

}

