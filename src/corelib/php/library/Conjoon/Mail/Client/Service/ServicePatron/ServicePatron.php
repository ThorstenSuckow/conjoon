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


namespace Conjoon\Mail\Client\Service\ServicePatron;

/**
 * A service patron is responsible for changing data retrieved from a service
 * server response to data applicable fro the client.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ServicePatron {

    /**
     * Returns the data for the specified key.
     * Clients should use this method when querying the array for specific
     * keys and throw the exception if the key does not exist to put the
     * object in an invalid state.
     *
     * @param array $data
     *
     * @return array
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws \Conjoon\Lang\MissingKeyException
     */
    public function getValueFor($key, array $data);

    /**
     * Returns the data prepared to be used by the client.
     *
     * @param array $data
     *
     * @return array
     *
     * @throws \Conjoon\Mail\Client\Service\ServicePatron\ServicePatronException
     */
    public function applyForData(array $data);

}