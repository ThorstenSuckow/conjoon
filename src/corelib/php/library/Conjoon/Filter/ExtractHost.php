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

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * Extracts protocol and host from a given url.
 *
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_ExtractHost implements Zend_Filter_Interface
{

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the protocl and host from the given url.
     * Returns the passed string if not found.
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $parts = parse_url($value);

        if ($parts === false || (is_array($parts) && !isset($parts['host']))) {
            return $value;
        }

        return
                (isset($parts['scheme']) ? $parts['scheme'] : 'http')
                . '://'
                . $parts['host']
                . '/'
                . (isset($parts['port']) ? ':' . $parts['port'] : '');
    }
}
