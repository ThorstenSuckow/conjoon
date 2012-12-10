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
 * A request interface for providing a default contract for all requests a
 * Server should handle.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Request {


    /**
     * Returns a list of parameters this request was configured with.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Returns the parameter for the specified key. Returns null if the parameter
     * was not found.
     *
     * @return array
     */
    public function getParameter($key);

    /**
     * Returns a textual representation of the command the request represents.
     *
     * @return string
     */
    public function getProtocolCommand();

    /**
     * Returns the user bound to this request.
     *
     * @return \Conjoon\User\User
     */
    public function getUser();


}