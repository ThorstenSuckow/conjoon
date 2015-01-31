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


namespace Conjoon\Mail\Client\Message\Flag;

/**
 * @see Conjoon_Mail_Exception
 */
require_once 'Conjoon/Mail/Exception.php';


/**
 * A pendnign message flag collection is a collection of message flags which
 * should be set for the specified mailbox path.
 * The client requests to set the flags by specifiying a collection of flags
 * and the folder where the ids of the flags can be found.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class FolderFlagCollection {

    /**
     * @var Conjoon_Mail_Client_Folder_ClientMailboxFolder
     */
    protected $_folder = null;

    /**
     * @var Conjoon_Mail_Client_Message_Flag_ClientMessageFlagCollection
     */
    protected $_collection = array();

    /**
     * Constructs a new instance of this class
     *
     * @param Conjoon_Mail_Client_Message_Flag_ClientMessageFlagCollection $collection
     * @param Conjoon_Mail_Client_Folder_ClientMailboxFolder $folder
     *
     * @throws Conjoon_Argument_Exception
     * @throws Conjoon_Mail_Client_Message_Flag_ClientMessageFlagException
     */
    public function __construct(
        FlagCollection $collection, \Conjoon\Mail\Client\Folder\Folder $folder)
    {
        $this->_collection = $collection;
        $this->_folder    = $folder;
    }

    /**
     * Returns the folder where the flags have to be set.
     *
     * @return Conjoon_Mail_Client_Folder_ClientMailboxFolder
     */
    public function getFolder()
    {
        return $this->_folder;
    }

    /**
     * Returns the message flag collections.
     *
     * @return Conjoon_Mail_Client_Message_Flag_ClientMessageFlagCollection
     */
    public function getFlagCollection()
    {
        return $this->_collection;
    }

}