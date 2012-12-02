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


namespace Conjoon\Mail\Server\Request;

/**
 * @see Conjoon\Mail\Server\Request\Request
 */
require_once 'Conjoon/Mail/Server/Request/Request.php';

/**
 * Interface for requests that want to set flags of a specific set of messages
 * in a specific folder.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface SetFlagsRequest extends Request {

    /**
     * Returns the folder flag collection which was sent with this request.
     *
     * @return \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection
     */
    public function getFolderFlagCollection();


}