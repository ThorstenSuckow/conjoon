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


namespace Conjoon\Mail\Server;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see Server
 */
require_once 'Conjoon/Mail/Server/Server.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * A default mail server implementation.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultServer implements Server {

    /**
     * @var \Conjoon\Mail\Server\Protocol\Protocol
     */
    protected $protocol;

    /**
     * @var string
     */
    protected $responseBodyClassName =
        '\Conjoon\Mail\Server\Response\DefaultResponseBody';

    /**
     * @var string
     */
    protected $responseClassName =
        '\Conjoon\Mail\Server\Response\DefaultResponse';

    /**
     * @var string
     */
    protected $errorResultClassName =
        '\Conjoon\Mail\Server\Protocol\DefaultResult\ErrorResult';

    /**
     * @var string
     */
    protected $expectedResponseType = "array";

    /**
     * Creates a new instance of this class.
     *
     * @param \Conjoon\Mail\Server\Protocol\Protocol $protocol The protocol
     *        this server speaks
     *
     * @return Response
     *
     * @throws Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(
        \Conjoon\Mail\Server\Protocol\Protocol $protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * Handles the request and returns the underlying protocol implementation's
     * result wrapped into a Response object
     * @param \Conjoon\Mail\Server\Request\Request $request
     *
     * @return Response
     */
    public function handle(\Conjoon\Mail\Server\Request\Request $request)
    {
        $responseBody  = $this->responseBodyClassName;
        $responseClass = $this->responseClassName;
        $errorResult   = $this->errorResultClassName;

        if (!method_exists($this->protocol, $request->getProtocolCommand())) {

            $error = new $errorResult(
                new \Conjoon\Mail\Server\Protocol\ProtocolException(
                    "The protocol does not understand the command "
                    ."\"" . $request->getProtocolCommand() ."\""
            ));

            return new $responseClass(
                $request,
                new $responseBody($error->toArray()),
                array('status' =>
                      \Conjoon\Mail\Server\Response\Response::STATUS_CODE_101)
            );
        }

        $command = $request->getProtocolCommand();

        $result = $this->protocol->$command(array(
            'user'       => $request->getUser(),
            'parameters' => $request->getParameters()
        ));

        if ($result instanceof \Conjoon\Mail\Server\Protocol\ErrorResult) {
            return new $responseClass(
                $request,
                new $responseBody($result->toArray()),
                array('status' =>
                      \Conjoon\Mail\Server\Response\Response::STATUS_CODE_100)
            );
        }

        return new $responseClass(
            $request,
            new $responseBody($result->toArray()),
            array('status' =>
                  \Conjoon\Mail\Server\Response\Response::STATUS_CODE_200)
        );
    }



}