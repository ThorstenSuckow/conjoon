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
