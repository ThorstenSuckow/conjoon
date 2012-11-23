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
 * Represents a client site folder.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Client_Folder_ClientMailboxFolder {

    /**
     * @var array
     */
    protected $_path;

    /**
     * @var string
     */
    protected $_rootId;

    /**
     * Constructs a new instance
     *
     * @param Conjoon_Mail_Client_Folder_ClientMailboxFolderPath $path The
     * client generated path this folder represents
     *
     */
    public function __construct(
        Conjoon_Mail_Client_Folder_ClientMailboxFolderPath $path)
    {
        $this->_path   = $path->getPath();
        $this->_rootId = $path->getRootId();

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
}

