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


namespace Conjoon\Mail\Server\Protocol;

/**
 * Encapsulates teh result of a Protocol operation. Implementing classes must be
 * able to provide a string-, an array- and a json representation of the result.
 * Implementing classes should tag their model with either ErrorResult or
 * SuccessResult to distinguish between a proper executed protocol command and
 * an erroneous result.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ProtocolResult {


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