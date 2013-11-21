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
 * Utility methods for retrieving current environment informations when in a network
 * context, i.e. this script is running in a webserver context.
 *
 * @package C\onjoon\Util
 * @subpackage Util
 * @category Util
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Environment {

    /**
     * Returns the scheme and the authority of the uri this script is
     * currently running an. Will fall back to $_SERVER information if no
     * further information is available.
     *
     * @return string
     *
     * @throws \Conjoon\Net\InvalidContextException if no information about the
     * current web context is available.
     */
    public function getCurrentUriBase() {

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

        return $scheme . '://' .$host .':' . $port;
    }

}
