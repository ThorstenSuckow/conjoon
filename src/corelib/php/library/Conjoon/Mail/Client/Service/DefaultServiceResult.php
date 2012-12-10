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


namespace Conjoon\Mail\Client\Service;

/**
 * Default implementation for a Service Result.
 * Note:
 * This class will try to suppress exceptions as much as possible. Any caught
 * exception while trying to create the result will instead be saved in the
 * result itself.
 *
 *
 * @package Conjoon
 * @category Conjoon\Service
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultServiceResult implements ServiceResult {

    /**
     * @var bool
     */
    protected $isSuccess;

    /**
     * @protected array
     */
    protected $data;

    /**
     * Creates a new instance of a ServiceResult.
     * The constructor will try to consider as much types as possible.
     * The follwing argument types are known to work:
     * - \Conjoon\Mail\Server\Response\Response The response of a request made
     * to an instance of \Conjoon\Mail\Server\Server
     * - \Exception any kind of exception
     *
     * @param mixed $from
     * @param mixed $patron An instance of Conjoon\Mail\Client\ServicePatron
     */
    public function __construct(
        $from, \Conjoon\Mail\Client\Service\ServicePatron\ServicePatron $patron = null)
    {
        $this->init($from);

        if ($patron && $this->isSuccess()) {
            try {
                $this->data = $patron->applyForData($this->data);
            } catch (\Exception $e) {
                $this->isSuccess = false;
                $this->data      = $this->traverseException($e);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function isSuccess()
    {
        return $this->isSuccess;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return array(
            'success' => $this->isSuccess,
            'data'    => $this->data
        );
    }

    /**
     * @inheritdoc
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }


    public function __toString()
    {
        return $this->toJson();
    }

// --------- helper

    /**
     * Cnfigures this object from the given argument.
     *
     * @param mixed $from
     */
    protected function init($from)
    {
        switch (true) {
            case (!is_object($from)):
                $this->isSuccess = false;
                $this->data    = array(
                    'message' => 'Error: Argument \"from\" expeced to be of '
                                  . 'type object'
                );
                break;

            case ($from instanceof \Exception):
                $this->isSuccess = false;
                $this->data      = $this->traverseException($from);
                break;

            case ($from instanceof \Conjoon\Mail\Server\Response\Response):
                $this->isSuccess = $from->isSuccess();
                $this->data    = $from->getResponseBody()->getData();
                break;

            default:
                $this->isSuccess = false;
                $this->data    = array(
                    'message' => 'Cannot extract information from passed '
                                 . 'argument \"from\"'
                );
                break;
        }
    }

    /**
     * Traverses the exception and returns it transformed into an array.
     *
     * @param \Exception $exception
     *
     * @return array
     */
    public function traverseException(\Exception $exception)
    {
        return array(
            'exception' => get_class($exception),
            'message'   => $exception->getMessage(),
            'code'      => $exception->getCode(),
            'previous'  => ($exception->getPrevious()
                ? $this->traverseException($exception->getPrevious())
                : null)
        );
    }

}