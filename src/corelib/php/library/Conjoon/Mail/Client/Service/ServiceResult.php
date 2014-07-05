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


namespace Conjoon\Mail\Client\Service;

/**
 * Encapsulates the result of a service request Implementing classes must be
 * able to provide a string-, an array- and a json representation of the result.
 * ServiceResults are meant to provide a communication interface between a
 * controller action and a service facade's method.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ServiceResult {

    /**
     * Returns true if the result indicates a successfull operation, otherwise
     * false.
     *
     * @return boolean
     */
    public function isSuccess();

    /**
     * Returns the data associated with the result.
     * This should be the data as returned by the request made by the service
     * and generalized to fit into an array structure.
     *
     * @return array
     */
    public function getData();

    /**
     * Returns an array representation of the result.
     *
     * @return array
     */
    public function toArray();

    /**
     * Returns a json representation of the result.
     *
     * @return string
     */
    public function toJson();

    /**
     * Returns a string representation of the result.
     *
     * @return string
     */
    public function __toString();

}