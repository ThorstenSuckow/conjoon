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

namespace Conjoon;

/**
 * @see Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class DatabaseTestCaseDefault extends \PHPUnit_Extensions_Database_TestCase {

    public function getConnection()
    {
        $dbTestSettings = parse_ini_file(
            dirname(__FILE__) . '/../dbunit.test.properties'
        );

        // set as default adapter for all db operations
        \Conjoon_Db_Table::setDefaultAdapter(
            \Zend_Db::factory('pdo_mysql', array(
                'host'           => $dbTestSettings['host'],
                'username'       => $dbTestSettings['user'],
                'password'       => $dbTestSettings['password'],
                'dbname'         => $dbTestSettings['database'],
                'port'           => $dbTestSettings['port'],
                'driver_options' => array(
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
                )
            )));

        $pdo = new \PDO(
            "mysql:" .
                "host=" . $dbTestSettings['host'] . ";".
                "dbname=".$dbTestSettings['database'].";".
                "port=".$dbTestSettings['port'],
            $dbTestSettings['user'], $dbTestSettings['password']
        );

        return $this->createDefaultDBConnection(
            $pdo,$dbTestSettings['database']
    );
    }

}