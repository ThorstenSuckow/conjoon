<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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


namespace Conjoon\Mail\Client\Folder;

/**
 * @see \Conjoon\Mail\Folder\FolderPath
 */
require_once 'Conjoon/Mail/Folder/FolderPath.php';

/**
 * Provides an oo representation of a client generated path for a mailbox.
 *
 * A client generated path is for example a json encoded array in the form of
 * '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
 *
 * This class will parse the string and provide access to the specific parts
 * of the path.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface FolderPath extends \Conjoon\Mail\Folder\FolderPath {

    /**
     * Returns the node id for the client generated path.
     * The node id is the las folder found in the path
     *
     * @returns string|null Returns the node id, or null, if no path is
     * available.
     *
     * @abstract
     */
    public function getNodeId();

    /**
     * Returns the root id for the client generated path.
     *
     * @returns string|null Returns the root id, or null, if no path is
     * available.
     *
     * @abstract
     */
    public function getRootId();


    /**
     * Returns an array representation of this object with the following keys:
     * - path
     * - nodeId
     * -rootId
     *
     * @return array
     *
     * @abstract
     */
    public function __toArray();

}

