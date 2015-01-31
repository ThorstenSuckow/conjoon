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


namespace Conjoon\Mail\Server\Request;

/**
 * A request interface for providing a default contract for all requests a
 * Server should handle.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Request {


    /**
     * Returns a list of parameters this request was configured with.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Returns the parameter for the specified key. Returns null if the parameter
     * was not found.
     *
     * @return array
     */
    public function getParameter($key);

    /**
     * Returns a textual representation of the command the request represents.
     *
     * @return string
     */
    public function getProtocolCommand();

    /**
     * Returns the user bound to this request.
     *
     * @return \Conjoon\User\User
     */
    public function getUser();


}