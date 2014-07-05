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

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_WhitespaceToHtml implements Zend_Filter_Interface {


    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the text with whitespace-pairs replaced by a pair of
     * " &nbsp;".
     *
     * Will not replace whitespaces that are inside of tags, i.e.
     *
     * <div class="test">This  i<br />s   a test</div>
     * will become
     * <div class="test">This &nbsp;i<br />s &nbsp; a test</div>
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        // change all whitespacs to none breaking spaces, the first
        // one and every second one will be untouched so that browsers
        // are still able to do a line breaks if white-space-wrapping
        // is enabled
        return preg_replace(
            "/((<[^>]*)| *)/ie",
            '"\2"=="\1"? "\1" : str_replace("  ", " &nbsp;", "\1")',
            $value
        );
    }
}