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

