<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Zend_Config_Ini
 */
require_once 'Zend/Config/Ini.php';

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
 * @see Intrabuild_Keys
 */
require_once 'Intrabuild/Keys.php';

/**
 * @see Intrabuild_Controller_Plugin_Auth
 */
require_once 'Intrabuild/Controller/Plugin/Auth.php';

/**
 * @see Intrabuild_Controller_Plugin_Lock
 */
require_once 'Intrabuild/Controller/Plugin/Lock.php';

/**
 * @see Intrabuild_User
 */
require_once 'Intrabuild/Modules/Default/User.php';

/**
 * This is he bootstrap file for the intrabuild-application.
 * It takes care of setting up all objects for each request and controls the
 * application flow.
 * It is important that each request runs over this file.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
// +----------------------------------------------------------------------------
// | Welcome! Start the session!
// +----------------------------------------------------------------------------
   Zend_Session::start();

// +----------------------------------------------------------------------------
// | Load up config and set up registry/ apply default configs to objects
// +----------------------------------------------------------------------------
   // load config
   $config = new Zend_Config_Ini('../application/config.ini', 'sandbox');

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
   $storage = new Zend_Auth_Storage_Session(Intrabuild_Keys::SESSION_AUTH_NAMESPACE);
   $auth->setStorage($storage);
   Zend_Registry::set(Intrabuild_Keys::REGISTRY_AUTH_OBJECT, $auth);

// +----------------------------------------------------------------------------
// | Set up the controller
// +----------------------------------------------------------------------------
   $controller = Zend_Controller_Front::getInstance();
   $controller->throwExceptions(false);
   $controller->addModuleDirectory('../application/modules');

   // add the plugins
   // authentication plugin, checks on each request if the user is logged in
   // in the preDispatch()-method
   $authenticationPlugin = new Intrabuild_Controller_Plugin_Auth($auth);
   $controller->registerPlugin($authenticationPlugin);
   // lock plugin: checks if the session of the user had been locked and denies
   // further access to any other controller than reception
   $lockPlugin = new Intrabuild_Controller_Plugin_Lock();
   $controller->registerPlugin($lockPlugin);

// +----------------------------------------------------------------------------
// | Set up Routing
// +----------------------------------------------------------------------------




// +----------------------------------------------------------------------------
// | We are all set, dispatch!
// +----------------------------------------------------------------------------
   $controller->dispatch();