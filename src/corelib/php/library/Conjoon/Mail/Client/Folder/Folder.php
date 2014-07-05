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
 * $URL: http://svn.conjoon.org/trunk/src/corelib/php/library/Conjoon/Mail/Client/Folder/Folder
 */


namespace Conjoon\Mail\Client\Folder;

/**
 * @see \Conjoon\Mail\Folder\FolderPath
 */
require_once 'Conjoon/Mail/Folder/FolderPath.php';

/**
 * Represents a client site folder.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Folder implements \Conjoon\Mail\Folder\FolderPath {

    /**
     * @var array
     */
    protected $_path;

    /**
     * @var string
     */
    protected $_rootId;

    /**
     * @var string
     */
    protected $_nodeId;

    /**
     * Constructs a new instance
     *
     * @param MailFolderPath $path The
     * client generated path this folder represents
     *
     */
    public function __construct(FolderPath $path)
    {
        $this->_path   = $path->getPath();
        $this->_rootId = $path->getRootId();
        $this->_nodeId = $path->getNodeId();

    }

    /**
     * Returns the root id for this folder
     *
     * @return string
     */
    public function getRootId()
    {
        return $this->_rootId;
    }

    /**
     * Returns the node id for this folder
     *
     * @return string
     */
    public function getNodeId()
    {
        return $this->_nodeId;
    }

    /**
     * Returns an array with the path parts. Path parts are the path to the
     * folder this object represents
     *
     * The array may be empty.
     *
     * @return array
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @inheritdoc
     */
    public function __toString() {
        return json_encode(
            array(
                'rootId' => $this->getRootId(),
                'path' => $this->getPath(),
                'nodeId' => $this->getNodeId()
            )
        );
    }
}

