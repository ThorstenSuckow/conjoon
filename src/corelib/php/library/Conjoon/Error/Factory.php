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
 * @see Conjoon_Error
 */
require_once 'Conjoon/Error.php';


/**
 * A static class for creating Conjoon_Error-objects.
 *
 * @package    Conjoon
 * @subpackage Error
 * @category   Error
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Error_Factory {




    /**
     * Creates a Conjoon_Error object based on the passed arguments and
     * return the error object.
     *
     * @param string $message
     * @param string $level
     * @param string $code
     * @param string $file
     * @param string $line
     *
     * @return Conjoon_Error
     */
    public static function createError($message = "[no message]", $level = null,
        $type = null, $code = null, $file = null, $line = null)
    {
        if ($level === null) {
            $level = Conjoon_Error::LEVEL_ERROR;
        }

        if ($type === null) {
            $type = Conjoon_Error::UNKNOWN;
        }

        $error = new Conjoon_Error();
        $error->setMessage($message);
        $error->setLevel($level);
        $error->setType($type);
        $error->setCode($code);
        $error->setFile($file);
        $error->setLine($line);

        return $error;
    }
}