<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * This is he bootstrap file for the conjoon-application.
 * It takes care of setting up all objects for each request and controls the
 * application flow.
 * It is important that each request runs over this file.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */

// +----------------------------------------------------------------------------
// | Check if app was installed
// +----------------------------------------------------------------------------
/*@BUILD_ACTIVE@
   if (!file_exists('./config.ini.php')) {
       die("<b>Error:</b><br />config.ini.php not found. Either create it manually or ".
           "run the installation script (" .
           "<a href=\"".$_SERVER['REQUEST_URI'] . "install/index.php\">".
           $_SERVER['REQUEST_URI'] . "install/index.php</a>)");
   } else if (file_exists('./install')) {
       die("<b>Error:</b><br /> Please delete the install directory first, or remove config.ini.php and refresh this page to run the installation wizard.");
   }
@BUILD_ACTIVE@*/


// +----------------------------------------------------------------------------
// | Register autoload
// +----------------------------------------------------------------------------
/*@BUILD_ACTIVE@
   function __autoload($className)
   {
       // note! $LIBRARY_PATH_BOOTSTRAP will be replaced during installation
       // with the path where the Zend/Conjoon libs used for this installation
       // of conjoon can be found. If any error occurres and $LIBRARY_PATH_BOOTSTRAP
       // was not replaced, you can do so by hand.
       $res = @include_once $LIBRARY_PATH_BOOTSTRAP . '/'
                            . str_replace('_', '/', $className) . '.php';
       if (!$res) {
           throw new Exception(
               "Could not load $className - I looked in $LIBRARY_PATH_BOOTSTRAP "
               ."but it does not seem to exits"
           );
       }
   }
@BUILD_ACTIVE@*/

// +----------------------------------------------------------------------------
// | Before doing anything else, load the config and set the include path if
// | necessary, so that the lib files can be loaded
// +----------------------------------------------------------------------------
   include_once './configCacheFunctions.php';

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

   // init the logger here!
   if ($config->log) {
       /**
        * @see Conjoon_Log
        */
       require_once 'Conjoon/Log.php';

       $cf = $config->toArray();

       Conjoon_Log::init($cf['log']);
   }

   // set as default adapter for all db operations
   Conjoon_Db_Table::setDefaultAdapter(
       Zend_Db::factory($config->database->adapter, array(
           'host'     => $config->database->params->host,
           'username' => $config->database->params->username,
           'password' => $config->database->params->password,
           'dbname'   => $config->database->params->dbname,
           'port'     => $config->database->params->port
   )));

   // set tbl prefix
   Conjoon_Db_Table::setTablePrefix($config->database->table->prefix);

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