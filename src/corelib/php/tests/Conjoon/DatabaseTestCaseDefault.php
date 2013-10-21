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

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

/**
 * @see Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';

/**
 * @see Doctrine\ORM\Configuration
 */
require_once 'Doctrine/ORM/Configuration.php';

/**
 * @see Doctrine\ORM\Tools\Setup
 */
require_once 'Doctrine/ORM/EntityManager.php';

/**
 * @see /Doctrine\ORM\Mapping\Driver\YamlDriver
 */
require_once 'Doctrine/ORM/Mapping/Driver/YamlDriver.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class DatabaseTestCaseDefault extends \PHPUnit_Extensions_Database_TestCase {

    protected $myDConn = null;

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->myDConn = null;
    }


    public function getConnection()
    {
        if ($this->myDConn) {
            return $this->myDConn;
        }

        $dbTestSettings = parse_ini_file(
            dirname(__FILE__) . '/../dbunit.test.properties'
        );

        // the connection configuration
        $dbParams = array(
            'driver'         => 'pdo_mysql',
            'host'           => $dbTestSettings['host'],
            'user'           => $dbTestSettings['user'],
            'password'       => $dbTestSettings['password'],
            'dbname'         => $dbTestSettings['database'],
            'port'           => $dbTestSettings['port'],
            'driver_options' => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
            )
        );

        $cache = new \Doctrine\Common\Cache\ArrayCache;
        $config = new Configuration;
        $config->setMetadataCacheImpl($cache);

        $config->setMetadataDriverImpl(
            new \Doctrine\ORM\Mapping\Driver\YamlDriver(array(
                'Conjoon\Data\Orm\Entity' => dirname(__FILE__)
                    . '/../../../../../src/www/application/orm'
            ))
        );
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir(dirname(__FILE__)
            . '/../../../../../src/corelib/php/library/Conjoon/Data/Entity/Proxy');
        $config->setProxyNamespace('\Conjoon\Data\Entity\Proxy');
        $config->setAutoGenerateProxyClasses(false);
        $this->_entityManager = EntityManager::create($dbParams, $config);

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

        $this->myDConn = $this->createDefaultDBConnection(
            $pdo, $dbTestSettings['database']
        );

        return $this->myDConn;
    }

}
