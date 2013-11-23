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
