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


namespace Conjoon\Mail\Server;

/**
 * A server interface for handling mail client requests.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Server {

    /**
     * Creates a new instance of this server.
     *
     * - protocol: Protocol
     *
     * @param Array $options An array of options this server should be
     *                       configured with
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(Array $options);


    /**
     * Handles the request sent to this server.
     * Implementing classes should take care of proper exception handling
     * and decide whether they capture any exception and encapsulate it into
     * an error response.
     *
     * @param \Conjoon\Mail\Server\Request\Request $request
     *
     * @return \Conjoon\Mail\Server\Response\Response
     */
    public function handle(\Conjoon\Mail\Server\Request\Request $request);


}