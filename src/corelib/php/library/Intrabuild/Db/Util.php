<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Inbox.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Modules/Groupware/Email/Item/Model/Inbox.php $
 */

/**
 * @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 * @see Intrabuild_Db_Exception
 */
require_once 'Intrabuild/Db/Exception.php';

/**
 * Database utility methods.
 *
 * @package Intrabuild
 * @subpackage Db
 * @category Db
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Db_Util {

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
                throw new Intrabuild_Db_Exception("No support for adapter \"$class\"");
            break;
        }

        return $bytes;
    }

}