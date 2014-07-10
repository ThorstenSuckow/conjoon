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

namespace Conjoon\Vendor\HtmlPurifier\UriFilter;

/**
 * @see \HTMLPurifier_URIFilter
 */
require_once 'HTMLPurifier/URIFilter.php';

/**
 * Uri Filter to prepend a url to resources represented by a relative url
 * which are not available on 'this' server.
 *
 * The prepended url with use the previous, relative url as the query part of the new
 * uri. Scripts should be able to read out the url as a parameter and show a specific
 * error message.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ResourceNotAvailableUriFilter extends \HTMLPurifier_URIFilter {

    /**
     * @type string
     */
    public $name = 'ResourceNotAvailableUriFilter';

    /**
     * @type \Conjoon\Net\Uri
     */
    protected $cnUri;

    /**
     * Creates a new instance of this filter.
     *
     * @param \Conjoon\Net\Uri $uri the uri along with the path to use to prepend
     * to the relative path
     *
     */
    public function __construct(\Conjoon\Net\Uri $uri) {

        $this->cnUri = $uri;
    }

    /**
     * @inheritdoc
     */
    public function filter(&$uri, $config, $context) {

        $cnUri = $this->cnUri;

        if (is_null($uri->scheme) || is_null($uri->host)) {

            $path = $cnUri->getPath();

            $query = 'url= ' . urlencode($uri->path);

            $uri = new \HtmlPurifier_URI(
                $cnUri->getScheme(),
                null,
                $cnUri->getHost(),
                $cnUri->getPort(),
                $path,
                $query,
                null
            );
        }

        return true;
    }


}
