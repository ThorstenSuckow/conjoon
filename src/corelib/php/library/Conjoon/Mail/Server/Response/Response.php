<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

use Conjoon\Argument\InvalidArgumentException;

/**
 * @see \Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * A response interface for providing a default contract for all responses a
 * Server can return.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class Response {

    /**
     * @var \Conjoon\Mail\Server\Request\Request
     */
    protected $request;

    /**
     * @var \Conjoon\Mail\Server\Response\ResponseBody
     */
    protected $responseBody;

    const STATUS_CODE_100 = 100;
    const STATUS_CODE_101 = 101;

    const STATUS_CODE_200 = 200;

    /**
     * @var array
     */
    protected $statusCodeToText = array(
        // error codes
        100 => 'unknown error',
        101 => 'command unknown',

        //success
        200 => 'ok'
    );


    /**
     * Creates a new response instance.
     *
     * @param \Conjoon\Mail\Server\Request\Request $request The request that
     *        triggered this response
     * @param string $body The response body, containing response details
     * @param array $options An additional set of options an instance of this
     *        class should be configured with.
     *        - status: an integer representing the response status
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(
        \Conjoon\Mail\Server\Request\Request $request,
        \Conjoon\Mail\Server\Response\ResponseBody $responseBody,
        array $options)
    {
        $this->request      = $request;
        $this->responseBody = $responseBody;

        if (!isset($options['status'])) {
            throw new InvalidArgumentException(
                "Status must be set."
            );
        } else {
            $this->status = (int) $options['status'];
        }
    }

    /**
     * Returns true if the response indicates a successfull response, otherwise
     * false.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status >= 200 && $this->status < 300;
    }

    /**
     * Returns true if the response indicates a response representing any kind
     * of error, otherwise false.
     *
     * @return bool
     */
    public function isError()
    {
        return $this->status >= 100 && $this->status < 200;
    }

    /**
     * Returns the request that triggered this response.
     *
     * @return \Conjoon\Mail\Server\Request\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the response body.
     *
     * @return \Conjoon\Mail\Server\Response\ResponseBody
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * Returns the status code this instance was configured with.
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }
}