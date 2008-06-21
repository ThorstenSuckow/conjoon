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
 * Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * Zend_Config_Ini
 */
require_once 'Zend/Config/Ini.php';

/**
 * Zend_Db_Table
 */
require_once 'Zend/Db/Table.php';

/**
 * Zend_Session
 */
require_once 'Zend/Session.php';

/**
 * Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * Zend_Auth_Storage_Session
 */
require_once 'Zend/Auth/Storage/Session.php'; 

/**
 * Intrabuild_Keys
 */ 
require_once 'Intrabuild/Keys.php'; 
 
/**
 * Intrabuild_Controller_Plugin_Auth
 */
require_once 'Intrabuild/Controller/Plugin/Auth.php';  
 
/**
 * Intrabuild_User
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
   
   // set the default timezone!
   date_default_timezone_set('Europe/Berlin');
   
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
   
// +----------------------------------------------------------------------------
// | Set up Routing
// +----------------------------------------------------------------------------

   

                           
// +----------------------------------------------------------------------------
// | We are all set, dispatch!
// +----------------------------------------------------------------------------
   $controller->dispatch();