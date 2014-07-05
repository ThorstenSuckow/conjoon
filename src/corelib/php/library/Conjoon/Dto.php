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
 * @see Conjoon_Util_Array
 */
require_once 'Conjoon/Util/Array.php';

/**
 * Base class for all Data Transfer Objects (DTOs) in the
 * conjoon project
 *
 * DataTransferObjects are also used for the rowClass property
 * of Zend_Db_Table_Abstract (@see Zend_Db_Table_Abstract::setRowClass)
 *
 * @uses ArrayAccess
 * @category   Dto
 * @package    Conjoon_Dto
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class Conjoon_Dto implements ArrayAccess {


// -------- interface ArrayAccess

    public function offsetExists($offset)
    {
        if (property_exists(get_class($this), $offset)) {
            return true;
        }
    }

    public function offsetGet($offset)
    {
        if (property_exists(get_class($this), $offset)) {
            return $this->{$offset};
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        if (property_exists(get_class($this), $offset)) {
            $this->{$offset} = $value;
        }

    }

    public function offsetUnset($offset)
    {
        if (property_exists(get_class($this), $offset)) {
            unset($this->{$offset});
        }
    }

}