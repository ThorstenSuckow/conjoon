<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
// | Before doing anything else, load the config and set the include path if
// | necessary, so that the lib files can be loaded
// +----------------------------------------------------------------------------
   /**
    * @todo cache the config
    */
   $initialConfig = parse_ini_file('./config.ini.php', true);

   // check if the library_path is set, and adjust the include_path if necessary
   if (($incPath = $initialConfig['environment']['include_path']) != null) {
       set_include_path(get_include_path() . PATH_SEPARATOR . $incPath);
   }

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Conjoon_Config_Array
 */
require_once 'Conjoon/Config/Array.php';

/**
 * @see Zend_Db_Table
 */
require_once 'Zend/Db/Table.php';

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
   $config = new Conjoon_Config_Array($initialConfig);
   Zend_Registry::set(Conjoon_Keys::REGISTRY_CONFIG_OBJECT, $config);

   // set as default adapter for all db operations
   Zend_Db_Table::setDefaultAdapter(
       Zend_Db::factory($config->database->adapter, array(
           'host'     => $config->database->params->host,
           'username' => $config->database->params->username,
           'password' => $config->database->params->password,
           'dbname'   => $config->database->params->dbname,
           'port'     => $config->database->params->port
   )));

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
                  $config->environment->application_path . '/application/modules'
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

// +----------------------------------------------------------------------------
// | Set up Routing
// +----------------------------------------------------------------------------



// +----------------------------------------------------------------------------
// | We are all set, dispatch!
// +----------------------------------------------------------------------------
   $controller->dispatch();