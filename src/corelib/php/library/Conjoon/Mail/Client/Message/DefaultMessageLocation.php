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

    /**
     * @inheritdoc
     */
    public function __toString() {
        return json_encode(
            array(
                'uId' => $this->getUId(),
                'folder' => $this->getFolder()->__toString()
            )
        );
    }

}

