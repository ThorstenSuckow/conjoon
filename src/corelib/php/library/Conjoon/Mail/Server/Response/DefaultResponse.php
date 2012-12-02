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

use Conjoon\Argument\InvalidArgumentException;

/**
 * @see \Conjoon\Mail\Server\Response\Response
 */
require_once 'Conjoon/Mail/Server/Response/Response.php';

/**
 * @see \Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';



/**
 * A default response implementation.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @uses Response
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultResponse implements Response {

    /**
     * @var \Conjoon\Mail\Server\Request\Request
     */
    protected $request;

    /**
     * @var \Conjoon\Mail\Server\Response\ResponseBody
     */
    protected $responseBody;


    /**
     *@inheritdoc
     */
    public function __construct(
        \Conjoon\Mail\Server\Request\Request $request,
        \Conjoon\Mail\Server\Response\ResponseBody $responseBody)
    {
        if (!($responseBody instanceof SuccessResponseBody)
            && !($responseBody instanceof ErrorResponseBody)) {
            throw new InvalidArgumentException(
                "responseBody must be of type "
                . "SuccessResponseBody or ErrorResponseBody"
            );
        }

        $this->request      = $request;
        $this->responseBody = $responseBody;
    }


    /**
     * @inheritdoc
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @inheritdoc
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @inheritdoc
     */
    public function isSuccess()
    {
        return ($this->responseBody
            instanceof \Conjoon\Mail\Server\Response\SuccessResponseBody);
    }

    /**
     * @inheritdoc
     */
    public function isError()
    {
        return ($this->responseBody
            instanceof \Conjoon\Mail\Server\Response\ErrorResponseBody);

    }
}