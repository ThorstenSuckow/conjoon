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
 * A response interface for providing a default contract for all responses a
 * Server can return.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Response {


    /**
     * Creates a new response instance.
     *
     * @param \Conjoon\Mail\Server\Request\Request $request The request that
     *        triggered this response
     * @param string $body The response body, containing response details
     *
     * @throws \Conjoon\Argument\InvalidArgumentException if ResponseBody is
     * not of the type SuccessResponseBody or ErrorResponseBody
     */
    public function __construct(
        \Conjoon\Mail\Server\Request\Request $request,
        \Conjoon\Mail\Server\Response\ResponseBody $responseBody);

    /**
     * Returns true if the response indicates a successfull response, otherwise
     * false.
     *
     * @return bool
     */
    public function isSuccess();

    /**
     * Returns true if the response indicates a response representing any kind
     * of error, otherwise false.
     *
     * @return bool
     */
    public function isError();

    /**
     * Returns the request that triggered this response.
     *
     * @return \Conjoon\Mail\Server\Request\Request
     */
    public function getRequest();

    /**
     * Returns the response body.
     *
     * @return \Conjoon\Mail\Server\Response\ResponseBody
     */
    public function getResponseBody();

}