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
 * @see Conjoon_Text_Template_Resource
 */
require_once 'Conjoon/Text/Template/Resource.php';

/**
 *
 * @category   Template
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Template_FileResource implements Conjoon_Text_Template_Resource {

    protected $_path;

    public function __construct($path)
    {
        $this->_path = $path;
    }

    /**
     *
     *
     * @return string
     * @throws Conjoon_Text_Template_Exception
     */
    public function getContent()
    {
        if (!file_exists($this->_path)) {
            /**
             * @see Conjoon_Text_Template_Exception
             */
            require_once 'Conjoon/Text/Template/Exception.php';

            throw new Conjoon_Text_Template_Exception(
                "\"" . $this->_path ."\" does not exist"
            );
        }

        $c = @file_get_contents($this->_path);

        if ($c === false) {
            /**
             * @see Conjoon_Text_Template_Exception
             */
            require_once 'Conjoon/Text/Template/Exception.php';

            throw new Conjoon_Text_Template_Exception(
                "problem reading \"" . $this->_path
            );
        }

        return $c;
    }
}