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
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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