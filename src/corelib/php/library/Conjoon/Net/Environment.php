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

namespace Conjoon\Net;

/**
 * @see \Conjoon\Net\Uri
 */
require_once  'Conjoon/Net/Uri.php';

use \Conjoon\Net\Uri;

/**
 * Utility methods for retrieving current environment informations when in a network
 * context, i.e. this script is running in a webserver context.
 *
 * @package \Conjoon\Net
 * @subpackage Net
 * @category Net
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Environment {

    /**
     * Returns the scheme and the authority of the uri this script is
     * currently running an. Will fall back to $_SERVER information if no
     * further information is available.
     *
     * @return \Conjoon\Net\Uri
     *
     * @throws \Conjoon\Net\Exception if no information about the
     * current web context is available or creating the Uri fails.
     */
    public function getCurrentUriBase() {

        try {
            if (!isset($_SERVER) || !isset($_SERVER['HTTP_HOST'])) {
                /**
                 * @see \Conjoon\Net\InvalidContextException
                 */
                require_once 'Conjoon/Net/InvalidContextException.php';

                throw new \Conjoon\Net\InvalidContextException(
                    "no SERVER information available."
                );
            }

            $host = $_SERVER['HTTP_HOST'];
            $scheme = isset($_SERVER['HTTPS']) ? 'https' : 'http';
            $port = $_SERVER['SERVER_PORT'];

            return new Uri(
                array(
                    'scheme' => $scheme,
                    'host' => $host,
                    'port' => $port
                )
            );
        } catch (\Exception $e) {

            /**
             * @see \Conjoon\Net\Exception
             */
            require_once 'Conjoon/Net/Exception.php';


            throw new \Conjoon\Net\Exception(
                "Exception thrown by previous exception", 0, $e
            );
        }
    }

}
