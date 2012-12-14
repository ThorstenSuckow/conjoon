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
 * @see \Conjoon\Mail\Client\Message\MessageLocation
 */
require_once 'Conjoon/Mail/Client/Message/MessageLocation.php';

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
class DefaultMessageLocation implements MessageLocation {

    /**
     * @var \Conjoon\Mail\Client\Folder\Folder
     */
    protected $folder;

    /**
     * @var string
     */
    protected $id;

    /**
     * Creates a new instance of this class.
     *
     * @param \Conjoon\Mail\Client\Folder\Folder $folder
     * @param mixed $id
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(\Conjoon\Mail\Client\Folder\Folder $folder, $id)
    {
        $data = array('id' => $id);

        ArgumentCheck::check(array(
            'id' => array(
                'type'       => 'string',
                'allowEmpty' => false
        )), $data);

        $id = $data['id'];

        $this->id = $id;

        $this->folder = $folder;
    }

    /**
     * @inheritdoc
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @inheritdoc
     */
    public function getUId()
    {
        return $this->id;
    }

}

