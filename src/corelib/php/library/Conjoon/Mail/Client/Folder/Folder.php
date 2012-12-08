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
     * Returns a textual representation for this instance.
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this)
               . ':'
               . '[rootId: ' . $this->getRootId() . '; '
               . 'path: ' . implode(',', $this->getPath()) . '; '
               . 'nodeId: ' . $this->getNodeId()
               . ']';
    }
}

