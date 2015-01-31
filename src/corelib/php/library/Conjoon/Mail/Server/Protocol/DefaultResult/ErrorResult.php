<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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