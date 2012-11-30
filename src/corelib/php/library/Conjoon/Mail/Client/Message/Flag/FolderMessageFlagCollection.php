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
class Conjoon_Mail_Client_Message_Flag_FolderMessageFlagCollection {

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
        Conjoon_Mail_Client_Message_Flag_ClientMessageFlagCollection $collection,
        \Conjoon\Mail\Client\Folder\MailFolder $folder)
    {
        $this->_collection = $collection;
        $this->_folder    = $folder;
    }

    /**
     * Returns the folder where the flags have to be set.
     *
     * @return Conjoon_Mail_Client_Folder_ClientMailboxFolder
     */
    public function getClientMailboxFolder()
    {
        return $this->_folder;
    }

    /**
     * Returns the message flag collections.
     *
     * @return Conjoon_Mail_Client_Message_Flag_ClientMessageFlagCollection
     */
    public function getClientMessageFlagCollection()
    {
        return $this->_collection;
    }

}