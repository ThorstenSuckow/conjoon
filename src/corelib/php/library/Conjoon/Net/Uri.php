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
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

use Conjoon\Argument\ArgumentCheck;

/**
 * Class representing a Uniform Resource Identifier (URI).
 *
 * @package Conjoon\Net
 * @subpackage Net
 * @category Net
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Uri {

    /**
     * @type string
     */
    protected $host = null;

    /**
     * @type string
     */
    protected $scheme = null;

    /**
     * @type integer
     */
    protected $port = null;

    /**
     * @type string
     */
    protected $path = null;

    /**
     * Returns the scheme and the authority of the uri this script is
     * currently running an. Will fall back to $_SERVER information if no
     * further information is available.
     *
     * @param array $uriConfig a uri config with the following keys:
     *  - scheme
     *  - host
     *  - port
     *  -path
     *
     *
     * @throws \Conjoon\Argument\InvalidArgumentException if scheme or host was omitted
     */
    public function __construct(array $uriConfig) {

        $data = $uriConfig;

        ArgumentCheck::check(array(
            'scheme' => array(
                'type' => 'string',
                'allowEmpty' => false
            ),
            'host' => array(
                'type' => 'string',
                'allowEmpty' => false
            ),
            'port' => array(
                'type' => 'integer',
                'mandatory' => false,
                'default' => null,
                'allowEmpty' => true
            ),
            'path' => array(
                'type' => 'string',
                'mandatory' => false,
                'default' => null,
                'allowEmpty' => true
            )
        ), $data);

        $this->scheme = strtolower($data['scheme']);
        $this->host = strtolower($data['host']);
        $this->port = $data['port'];
        $this->path = is_string($data['path'])
                      ? strtolower($data['path'])
                      : $data['path'];

    }

    /**
     * @return string
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @return integer
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Returns a new Uri instance with all information from
     * this, except for path which gets set to the path specified.
     *
     * @return \Conjoon\Net\Uri
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function setPath($path) {
        return new Uri(array(
            'scheme' => $this->getScheme(),
            'host' => $this->getHost(),
            'port' => $this->getPort(),
            'path' => $path
        ));
    }
}
