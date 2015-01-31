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
 * This is he bootstrap file for the conjoon-application.
 * It takes care of setting up all objects for each request and controls the
 * application flow.
 * It is important that each request runs over this file.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

// +----------------------------------------------------------------------------
// | Check if app was installed
// +----------------------------------------------------------------------------
/*@BUILD_ACTIVE@
   if (!file_exists('./config.ini.php')) {
       die("<b>Error:</b><br />config.ini.php not found. "
           . "Run the Setup Assistant or create it manually.");
   }
@BUILD_ACTIVE@*/


// +----------------------------------------------------------------------------
// | Register autoload
// +----------------------------------------------------------------------------
/*@REMOVE@*/
$LIBRARY_PATH_BOOTSTRAP = dirname(__FILE__) . '/../../../';
/*@REMOVE@*/
/*@BUILD_ACTIVE@
   function __conjoon_autoload($className)
   {
        // note! LIBRARY_PATH_BOOTSTRAP will be replaced during installation
        // with the path where the Zend/Conjoon libs used for this installation
        // of conjoon can be found. If any error occurres and LIBRARY_PATH_BOOTSTRAP
        // was not replaced, you can do so by hand.
        if (strpos($className, '\\') !== false) {
            $className = str_replace('\\', '/', ltrim($className, '\\'));
        } else {
            $className = str_replace('_', '/', $className);
        }

        if (strpos($className, 'Symfony') === 0) {
            $className = 'Doctrine/' . $className;
        }

        $res = @include_once $LIBRARY_PATH_BOOTSTRAP . '/'
               . $className . '.php';
   }

   spl_autoload_register('__conjoon_autoload');

@BUILD_ACTIVE@*/

// +----------------------------------------------------------------------------
// | Before doing anything else, load the config and set the include path if
// | necessary, so that the lib files can be loaded
// +----------------------------------------------------------------------------

   include_once './configCacheFunctions.php';

    try {
        $config = conjoon_initConfigCache();
    } catch (Exception $e) {
        // definitely abort here!
        die($e->getMessage());
    }

   $config = conjoon_initConfigCache();

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Conjoon_Config_Array
 */
require_once 'Conjoon/Config/Array.php';

/**
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';

/**
 * @see Zend_Session
 */
require_once 'Zend/Session.php';

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * @see Zend_Auth_Storage_Session
 */
require_once 'Zend/Auth/Storage/Session.php';

/**
 * @see Conjoon_Keys
 */
require_once 'Conjoon/Keys.php';

/**
 * @see Conjoon_Controller_Plugin_Auth
 */
require_once 'Conjoon/Controller/Plugin/Auth.php';

/**
 * @see Conjoon_Controller_Plugin_Lock
 */
require_once 'Conjoon/Controller/Plugin/Lock.php';

/**
 * @see Conjoon_User
 */
require_once 'Conjoon/Modules/Default/User.php';

/**
 * @see Zend_Loader_PluginLoader
 */
require_once 'Zend/Loader/PluginLoader.php';

/*@REMOVE@*/
/**
 * @see Zend_Controller_Exception
 */
require_once 'Zend/Controller/Exception.php';
/*@REMOVE@*/

// +----------------------------------------------------------------------------
// | Welcome! Start the session!
// +----------------------------------------------------------------------------
   Zend_Session::start();

// +----------------------------------------------------------------------------
// | Apply default configs to objects
// +----------------------------------------------------------------------------
   // load config
   if (!($config instanceof Conjoon_Config_Array)) {
        $config = new Conjoon_Config_Array($config);
   }
   Zend_Registry::set(Conjoon_Keys::REGISTRY_CONFIG_OBJECT, $config);

   if ($config->application->zf->use_plugin_cache) {
       if (isset($_SESSION[Conjoon_Keys::SESSION_AUTH_NAMESPACE])) {
           $baseUrl = trim($config->environment->base_url);
           $parts = explode('/',
               ltrim(
                ($baseUrl != "/"
                ? str_replace($baseUrl, '', $_SERVER['REQUEST_URI'])
                : $_SERVER['REQUEST_URI']),
                './'),
           4);
           array_pop($parts);
           $file = implode('_', $parts);
       } else {
           $file = "";
       }
       $classFileIncCache= './_configCache/pluginLoader/'
                           . ($file ? $file : 'default')
                           . '.cache.php';
       if (file_exists($classFileIncCache)) {
         include_once $classFileIncCache;
       }
       Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
    }


   // init the logger here!
   if ($config->log) {

       /**
        * @see Conjoon_Log
        */
       require_once 'Conjoon/Log.php';

       // load Zend/Log for log constants so we don't have to
       // require this later on
       /**
        * @see Zend_Log
        */
       require_once 'Zend/Log.php';

       if (!Conjoon_Log::isConfigured()) {
           // we might have already configured the logger when
           // processing the configuration fle
           $cf = $config->toArray();
           Conjoon_Log::init($cf['log']);
       }

   }

   // set as default adapter for all db operations
   Conjoon_Db_Table::setDefaultAdapter(
       Zend_Db::factory($config->database->adapter, array(
           'host'           => $config->database->params->host,
           'username'       => $config->database->params->username,
           'password'       => $config->database->params->password,
           'dbname'         => $config->database->params->dbname,
           'port'           => $config->database->params->port,
           'driver_options' => array(
               PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
           )
   )));

   // set tbl prefix
   Conjoon_Db_Table::setTablePrefix($config->database->table->prefix);

    // +------------------------------------------------------------------------
    // | DOCTRINE
    // +------------------------------------------------------------------------
    /**
     * @see Doctrine\ORM\Tools\Setup
     */
    require_once 'Doctrine/ORM/Tools/Setup.php';

    \Doctrine\ORM\Tools\Setup::registerAutoloadDirectory(
        /*@REMOVE@*/
            $LIBRARY_PATH_BOOTSTRAP . '/vendor/doctrine/'
        /*@REMOVE@*/
        /*@BUILD_ACTIVE@
        $LIBRARY_PATH_BOOTSTRAP
        @BUILD_ACTIVE@*/
    );

    /**
     * @see Doctrine\Common\ClassLoader
     */
    require_once 'Doctrine/Common/ClassLoader.php';

    $classLoader = new \Doctrine\Common\ClassLoader(
        'Conjoon',
        /*@REMOVE@*/
        $LIBRARY_PATH_BOOTSTRAP . '/src/corelib/php/library/'
        /*@REMOVE@*/
        /*@BUILD_ACTIVE@
        $LIBRARY_PATH_BOOTSTRAP
        @BUILD_ACTIVE@*/
    );
    $classLoader->register();

    $doctrineParams = array(
        'driver'         => 'pdo_mysql',
        'host'           => $config->database->params->host,
        'user'           => $config->database->params->username,
        'password'       => $config->database->params->password,
        'dbname'         => $config->database->params->dbname,
        'port'           => $config->database->params->port,
        'driverOptions'  => array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
        )
    );


    $doctrineCacheInstances = array(
        'query_cache' => null,
        'metadata_cache' => null
    );

    // doctrine cache settings

    if ($config->application->doctrine->cache->enabled) {
        $doctrineCacheSections = array(
            'query_cache'    => $config->application->doctrine->cache->query_cache,
            'metadata_cache' => $config->application->doctrine->cache->metadata_cache
        );

        foreach($doctrineCacheSections as $doctrineCacheKey => $doctrineCacheSection) {
            if ($doctrineCacheSection->enabled) {

                // if we do not find a valid extension, we'll be usind
                // an array cache later on
                switch ($doctrineCacheSection->type) {
                    case 'apc':
                    case 'memcache':
                    case 'memcached':
                            $className = '\Doctrine\Common\Cache\\' .
                                ucfirst($doctrineCacheSection->type) .
                                'Cache';
                            $doctrineCacheInstances[$doctrineCacheKey] =
                                new $className;
                        break;
                    case 'file':
                        $doctrineCacheInstances[$doctrineCacheKey] =
                            new \Doctrine\Common\Cache\FilesystemCache(
                                $doctrineCacheSection->dir
                            );
                        break;
                    default:
                        break;
                }
            }
        }

    }

    if (!$doctrineCacheInstances['query_cache']) {
        $doctrineCacheInstances['query_cache'] =
            new \Doctrine\Common\Cache\ArrayCache;
    }

    if (!$doctrineCacheInstances['metadata_cache']) {
        $doctrineCacheInstances['metadata_cache'] =
            new \Doctrine\Common\Cache\ArrayCache;
    }

    $doctrineConfig = new \Doctrine\ORM\Configuration;
    $doctrineConfig->setMetadataCacheImpl(
        $doctrineCacheInstances['metadata_cache']);

    $doctrineConfig->setMetadataDriverImpl(
        new \Doctrine\ORM\Mapping\Driver\YamlDriver(
             $config->environment->application_path
             . '/orm'
        )
    );
    $doctrineConfig->setQueryCacheImpl(
        $doctrineCacheInstances['query_cache']);
    $doctrineConfig->setProxyDir(
        /*@REMOVE@*/
        $LIBRARY_PATH_BOOTSTRAP . '/src/corelib/php/library/'
        /*@REMOVE@*/
        /*@BUILD_ACTIVE@
        $LIBRARY_PATH_BOOTSTRAP
        @BUILD_ACTIVE@*/
        . '/Conjoon/Data/Entity/Proxy'
    );
    $doctrineConfig->setProxyNamespace('\Conjoon\Data\Entity\Proxy');
    $doctrineConfig->setAutoGenerateProxyClasses(false);
    Zend_Registry::set(Conjoon_Keys::DOCTRINE_ENTITY_MANAGER,
        \Doctrine\ORM\EntityManager::create($doctrineParams, $doctrineConfig)
    );

    // +---------------------------------------------------------
    // | HTMLPURIFIER
    // +------------------------------------------------------------------------
    if ($config->application->htmlpurifier->preload_all) {
        /**
         * @see HTMLPurifier.includes
         */
        /*@IGNORE*/require_once 'HTMLPurifier.includes.php';
    }

    /**
     * @see HTMLPurifier_Bootstrap
     */
    /*@IGNORE*/require_once 'HTMLPurifier/Bootstrap.php';

    /**
     * @see HTMLPurifier.autoload
     */
    /*@IGNORE*/require_once 'HTMLPurifier.autoload.php';

    // +------------------------------------------------------------------------
    // | CACHING
    // +------------------------------------------------------------------------
    /**
     * @see Conjoon_Cache_Factory
     */
    require_once 'Conjoon/Cache/Factory.php';

    $mdCache = Conjoon_Cache_Factory::getCache(
        Conjoon_Keys::CACHE_DB_METADATA,
        $config->toArray()
    );

    if ($mdCache) {
        Conjoon_Db_Table::setDefaultMetadataCache($mdCache);
    }

   // set up authentication storage
   $auth = Zend_Auth::getInstance();
   //set session storage
   $storage = new Zend_Auth_Storage_Session(Conjoon_Keys::SESSION_AUTH_NAMESPACE);
   $auth->setStorage($storage);
   Zend_Registry::set(Conjoon_Keys::REGISTRY_AUTH_OBJECT, $auth);

// +----------------------------------------------------------------------------
// | Localization
// +----------------------------------------------------------------------------
    // set the default timezone here
    // if the configured timezone is not valid, we will gracefully
    // fall back to $LOCALE_DEFAULT_TIMEZONE as the default timezone,
    // which was configured during the installation process
    $tz = $config->application->locale->date->timezone;
    if ($tz) {
        $tzres = @date_default_timezone_set($tz);

        if ($tzres !== true) {

            /*@REMOVE@*/
            $deftz = 'Europe/Berlin';
            /*@REMOVE@*/

            /*@BUILD_ACTIVE@
            $deftz = $LOCALE_DEFAULT_TIMEZONE;
            @BUILD_ACTIVE@*/

            if ($config->log) {
                Conjoon_Log::log(
                    "\"date_default_timezone_set()\" failed to set application's "
                    . "default timezone \""
                    . $config->application->locale->date->timezone."\"; "
                    ." Falling back to \"".$deftz."\" instead.",
                    Zend_Log::NOTICE
                );
            }

            // last resort
            $lr = @date_default_timezone_set($deftz);
            if ($lr !== true) {
                // failed? Exit.
                die(
                    "I could not start up the application due to an error that "
                    . "occurred during setting the default timezone. Sorry. I tried "
                    . "to gracefully fall back to an alternative timezone, "
                    . "but I failed. You should take care of this issue "
                    . "before you continue working with conjoon."
                );
            }
        }
    }

// +----------------------------------------------------------------------------
// | Set up the controller
// +----------------------------------------------------------------------------
   $controller = Zend_Controller_Front::getInstance();
   $controller->throwExceptions(false)
              ->addModuleDirectory(
                  $config->environment->application_path . '/modules'
              )
              ->setBaseUrl($config->environment->base_url);

   // add the plugins
   // authentication plugin, checks on each request if the user is logged in
   // in the preDispatch()-method
   $authenticationPlugin = new Conjoon_Controller_Plugin_Auth($auth);
   $controller->registerPlugin($authenticationPlugin);
   // lock plugin: checks if the session of the user had been locked and denies
   // further access to any other controller than reception
   $lockPlugin = new Conjoon_Controller_Plugin_Lock();
   $controller->registerPlugin($lockPlugin);


   // add the ExtDirect plugins
   $loadExtDirect = false;
   if ($config->application->ext->direct->request->autoLookup) {
       if (isset($_POST[$config->application->ext->direct->request->parameter])) {
           $loadExtDirect = true;
       }
   } else {
       $loadExtDirect = true;
   }

   if ($loadExtDirect) {
       /**
        * @see Conjoon_Controller_Plugin_ExtRequest
        */
       require_once 'Conjoon/Controller/Plugin/ExtRequest.php';

       $extDirect = new Conjoon_Controller_Plugin_ExtRequest(array(
           'extParameter'      => $config->application->ext->direct->request
                                         ->parameter,
           'additionalHeaders' => array('Content-Type' => 'application/json'),
           'additionalParams'  => array('format' => 'json'),
           'action'            => 'multi.request',
           'controller'        => 'ext',
           'module'            => 'default',
           'singleException'   => $config->application->ext->direct->request->singleException
       ));

       Zend_Registry::set(Conjoon_Keys::EXT_REQUEST_OBJECT, $extDirect);
       $extDirect->registerPlugins();
   }


    // add helper namespace
    Zend_Controller_Action_HelperBroker::addPrefix('Conjoon_Controller_Action_Helper');

    /*@REMOVE@*/
    // set the connection check default properties
    if ($config->application->connection_check->enabled) {
        $c =& $config->application->connection_check;

        /**
         * @see Conjoon_Controller_Action_Helper_ConnectionCheck
         */
        require_once 'Conjoon/Controller/Action/Helper/ConnectionCheck.php';

        Conjoon_Controller_Action_Helper_ConnectionCheck::setConfig(array(
            'enabled' => true,
            'ip'      => $c->ip,
            'port'    => $c->port,
            'timeout' => $c->timeout
        ));
    }
    /*@REMOVE@*/

// +----------------------------------------------------------------------------
// | Set up Routing
// +----------------------------------------------------------------------------



// +----------------------------------------------------------------------------
// | We are all set, dispatch!
// +----------------------------------------------------------------------------
   /**
    * @see Conjoon_Controller_DispatchHelper
    */
   require_once 'Conjoon/Controller/DispatchHelper.php';

   Conjoon_Controller_DispatchHelper::dispatch();
