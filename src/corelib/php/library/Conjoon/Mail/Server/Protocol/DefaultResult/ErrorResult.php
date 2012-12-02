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


namespace Conjoon\Mail\Server\Protocol\DefaultResult;

/**
 * @see \Conjoon\Mail\Server\Protocol\ErrorResult
 */
require_once 'Conjoon/Mail/Server/Protocol/ErrorResult.php';

/**
 * A default implematation of an error result.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ErrorResult implements \Conjoon\Mail\Server\Protocol\ErrorResult {

    /**
     * @var \Exception
     */
    protected $exception = null;

    /**
     * Creates a new instance of an ErrorResult.
     *
     * @param \Exception $e
     */
    public function __construct(\Exception $e)
    {
        $this->exception = $e;
    }

    /**
     * @inheritdoc
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Traverses the exception and looks up previous ecpections.
     * Collected information will be returned as an array.
     *
     * @param \Exception $exception
     *
     * @return array
     */
    protected function traverseException(\Exception $exception)
    {
        return array(
            'exceptionClass'    => get_class($exception),
            'message'           => $exception->getMessage(),
            'code'              => $exception->getCode(),
            'previousException' => ($exception->getPrevious()
                                    ? $this->traverseException($exception->getPrevious())
                                    : null)
        );
    }


    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return $this->traverseException($this->exception);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->toJson();
    }

}