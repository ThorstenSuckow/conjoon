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


namespace Conjoon\Mail\Folder;

/**
 * @see \Conjoon\Mail\Folder\FolderPath
 */
require_once 'Conjoon/Mail/Folder/FolderPath.php';

/**
 * Provides an oo representation of a fodler path. A path to a folder exists
 * of various path parts, represented by an array.
 *
 * @category   Conjoon\Mail
 * @package    Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface FolderPath {

    /**
     * Returns an array with the path parts. Path parts are the path to the
     * folder this object represents.
     * The array may be empty. In this case, thepath represents the root path.
     *
     * @return array
     *
     * @abstract
     */
    public function getPath();

    /**
     * Returns a textual representation of this instance.
     * @return string
     */
    public function __toString();

}

