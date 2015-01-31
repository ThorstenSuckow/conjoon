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
 * @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 * @see Conjoon_Db_Exception
 */
require_once 'Conjoon/Db/Exception.php';

/**
 * Database utility methods.
 *
 * @package Conjoon
 * @subpackage Db
 * @category Db
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Db_Util {

    /**
     * Returns the maximum allowed byte sequence for the passed db-adapter.
     *
     * @param $adapter Zend_Db_Adapter_Abstract
     *
     * @return float The number of bytes the db represented by the adapter
     * can dafely store.
     *
     * @todo check for a safer way to query the used db type
     */
    public static function getMaxAllowedPacket(Zend_Db_Adapter_Abstract $adapter)
    {
        $class = strtolower(get_class($adapter));

        $dbType = "";

        if (strpos($class, 'mysql') !== false) {
            $dbType = 'mysql';
        }

        $bytes = 0;

        switch ($dbType) {
            case 'mysql':
                $stmt = $adapter->query("SHOW VARIABLES WHERE Variable_name = 'max_allowed_packet'");
                $row = $stmt->fetch();
                $bytes = (float)$row['Value'];
            break;

            default:
                throw new Conjoon_Db_Exception("No support for adapter \"$class\"");
            break;
        }

        return $bytes;
    }

}