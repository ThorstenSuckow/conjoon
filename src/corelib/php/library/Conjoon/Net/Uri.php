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
     * @type string
     */
    protected $query = null;

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
            ),
            'query' => array(
                'type' => 'string',
                'mandatory' => false,
                'default' => null,
                'allowEmpty' => true
            )
        ), $data);

        $this->scheme = strtolower($data['scheme']);
        $this->host = strtolower($data['host']);
        $this->port = $data['port'];
        $this->path = $data['path'];
        $this->query = $data['query'];

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
     * @return string
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Returns a new Uri instance with all information from
     * this, except for path which gets set to the path specified.
     *
     * @param string $path
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
            'path' => $path,
            'query' => $this->getQuery(),
        ));
    }

    /**
     * Returns a new Uri instance with all information from
     * this, except for query which gets set to the query specified.
     *
     * @param string $query
     *
     * @return \Conjoon\Net\Uri
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function setQuery($query) {
        return new Uri(array(
            'scheme' => $this->getScheme(),
            'host' => $this->getHost(),
            'port' => $this->getPort(),
            'path' => $this->getPath(),
            'query' => $query
        ));
    }
}
