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
