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


namespace Conjoon\Mail\Server\Response;

/**
 * An interface for a response body which is a simple wrapper for a text.
 * See SuccessResponseBody and ErrorResponseBody for concrete implementations.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ResponseBody {


    /**
     * Creates a new response body instance.
     *
     * @param array $data
     */
    public function __construct(Array $data = array());

    /**
     * Returns the response body's data.
     *
     * @return array
     */
    public function getData();

}