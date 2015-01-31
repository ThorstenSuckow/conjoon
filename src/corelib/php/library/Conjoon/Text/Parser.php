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
 * A simple class providing the interface for classes that operate and parse
 * text strings.
 *
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class Conjoon_Text_Parser {

    /**
     * @type mixed
     */
    protected $_options;

    /**
     * @param array $options
     *
     * @throws Conjoon_Argument_Exception
     */
    public function __construct($options = array())
    {
        $this->_options = $options;
    }

    /**
     * Takes an input argument and returns the parsed result.
     *
     * @param string $input
     *
     * @return mixed
     *
     * @throws Conjoon_Text_Parser_Exception, Conjoon_Argument_Exception
     */
    abstract public function parse($input);

}