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
 * This is a very simple bootstrap file for the conjoon setup process.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

if (version_compare(PHP_VERSION, '5.2.5' , '<')) {
    die(
        "Sorry, your version of PHP is " . PHP_VERSION .". "
        ."You need at least PHP 5.2.5 to run conjoon."
    );
}

include_once './functions.php';

@session_start();

if (isset($_GET['nosession'])) {
    session_destroy();
    header("Location: ./index.php");
    die();
}

// +----------------------------------------------------------------------------
// | Check if user is currently  running an instance of conjoon
// +----------------------------------------------------------------------------
/*@BUILD_ACTIVE@
   if (array_key_exists('com.conjoon.session.authNamespace', $_SESSION)) {
        die(
           "The installation wizard has detected that you are currently running an instance "
           ."of conjoon. Please sign out of conjoon first, then reload this page. If you have "
           ."already closed your running instance of conjoon, deleting your cookies might help."
           ."<br />You can also try to reload this page by clicking "
           ."<a href=\"./index.php?nosession=1\">- here -</a>. Clicking "
           ."this link will remove your conjoon session data."
       );
   }
@BUILD_ACTIVE@*/

// +----------------------------------------------------------------------------
// | init install context
// +----------------------------------------------------------------------------
$action = isset($_GET['action']) ? trim((string)$_GET['action']) : '';

// +----------------------------------------------------------------------------
// | Check if user is authorized
// +----------------------------------------------------------------------------
if (!array_key_exists('com.conjoon.session.install.authorized', $_SESSION)) {
    $action = $action == 'authorize_success' ? $action : 'authorize';
} else if (array_key_exists('com.conjoon.session.install.authorized', $_SESSION)
    && ($action == 'authorize' || $action == 'authorize_success')) {
    $action = '';
}

// +----------------------------------------------------------------------------
// | PREPARE SESSION
// +----------------------------------------------------------------------------
    if (array_key_exists('com.conjoon.session.install.authorized', $_SESSION)) {

        if (file_exists('../installation.info.php') && !isset($_SESSION['installation_info'])) {
            include_once '../installation.info.php';
            $_SESSION['installation_info'] = $INSTALLATION_INFO[count($INSTALLATION_INFO)-1];
            $_SESSION['installation_info']['previous_version'] = $INSTALLATION_INFO[count($INSTALLATION_INFO)-1]['version'];
            $_SESSION['installation_info']['first_version']    = $INSTALLATION_INFO[0]['version'];
            $_SESSION['app_credentials'] = $INSTALLATION_INFO[count($INSTALLATION_INFO)-1]['app_credentials'];
        }

        if (file_exists('../config.ini.php') && !isset($_SESSION['config_info'])) {
            $_SESSION['remove_config_ini_php'] = true;
            $_SESSION['config_info'] = @parse_ini_file('../config.ini.php', true);
        }

        if (!isset($_SESSION['config_info'])) {
            $_SESSION['remove_config_ini_php'] = false;
            $_SESSION['config_info']           = false;
        }

        if (!isset($_SESSION['installation_info'])) {
            $_SESSION['installation_info'] = array();
        }


        if (!isset($_SESSION['setup_ini'])) {
            $_SESSION['setup_ini'] = parse_ini_file('./setup.ini', true);
        }

        /*@REMOVE@*/
        if (!isset($_SESSION['current_version'])) {
            $_SESSION['current_version'] = '0.0';
        }
        /*@REMOVE@*/

        /*@BUILD_ACTIVE@
        if ($action != 'install_success' && $action != 'finish') {
            $ret = @include_once './files/'.$_SESSION['setup_ini']['lib_path']['folder'].'/Conjoon/Version.php';
            if (!$ret || !file_exists('./files/'.$_SESSION['setup_ini']['app_path']['folder'])) {
                die("Could not find libraries. ".
                    "Make sure you are working on a ".
                    "fresh copy of the install folder.");
            }
        }

        if (!isset($_SESSION['current_version'])) {
            $_SESSION['current_version'] = Conjoon_Version::VERSION;
        }
        @BUILD_ACTIVE@*/
   }

// +----------------------------------------------------------------------------
// | Build the view...
// +----------------------------------------------------------------------------
   $VIEW = array(
       'action' => $action
   );


   // build navigation for available set up steps
   // its important that the navigation appears in the order the various steps
   // are processed
   // the first index is the current request's action
   $VIEW['navigation'] = array(
        '' => array(
            "Welcome", "./index.php", "./index.php?action=welcome_check"
        ),
        'check' => array(
            "Step 1", "./index.php?action=check", "./?action=check_verify"
        ),
        'localization' => array(
            "Step 2", "./index.php?action=localization", "./?action=localization_check"
        ),
        'database' => array(
            "Step 3", "./index.php?action=database", "./?action=database_check"
        ),
        'app_path' => array(
            "Step 4", "./index.php?action=app_path", "./index.php?action=app_path_check"
        ),
        'cache' => array(
            "Step 5", "./index.php?action=cache", "./index.php?action=cache_check"
        ),
        'lib_path' => array(
            "Step 6", "./index.php?action=lib_path", "./index.php?action=lib_path_check"
        ),
        'doc_path' => array(
            "Step 7", "./index.php?action=doc_path", "./index.php?action=doc_path_check"
        )
   );

   $changeAppCredentials = !isset($_SESSION['installation_info']['app_credentials']['user']);
   if ($changeAppCredentials) {
       $VIEW['navigation']['app_credentials'] = array(
           "Step 8", "./index.php?action=app_credentials", "./index.php?action=app_credentials_check"
       );
   }

   if (isset($_SESSION['installation_info']['previous_version'])) {
      $VIEW['navigation']['patch'] = array(
          "Patching", "./index.php?action=patch", "./index.php?action=patch_check"
      );
   }

   $VIEW['navigation']['install'] = array(
       "Install!", "./index.php?action=install", "./index.php?action=install_process"
   );

   $VIEW['navigation']['finish'] =array("Finish", "./index.php?action=finish");


// +----------------------------------------------------------------------------
// | process action
// +----------------------------------------------------------------------------

   ob_start();
   switch ($action) {

       case 'authorize':
           $VIEW['navigation'] = array(
               'authorize' => array(
                   "Authentication", "./index.php?action='authorize",
                   "./index.php?action=authorize_check"
               )
           );
           include_once './authorize.php';
           break;

       case 'authorize_success':
           header("Location: ./index.php");
           die();
           break;

       // actions for checking prerequisites
       case 'check':
           include_once './check.php';
       break;

       case 'check_verify':
            $VIEW['action'] = 'check';
            include_once './check.php';
       break;

       case 'check_success':
            header("Location: ./index.php?action=localization");
            die();
       break;

       // actions for localization
       case 'localization':
            include_once './localization.php';
       break;
       case 'localization_success':
           header("Location: ./index.php?action=database");
           die();
       break;
       case 'localization_check':
           $VIEW['action'] = 'localization';
           include_once './localization.php';
       break;

       // actions for setting up database
       case 'database':
           include_once './database.php';
       break;
       case 'dbcheck_success':
           header("Location: ./index.php?action=app_path");
           die();
       break;
       case 'database_check':
           $VIEW['action'] = 'database';
           include_once './database.php';
       break;

       // actions for setting up path to application folder
       case 'app_path':
           include_once './app_path.php';
       break;
       case 'app_path_check':
           $VIEW['action'] = 'app_path';
           include_once './app_path.php';
       break;
       case 'app_path_success':
           header("Location: ./index.php?action=cache");
           die();
       break;

       // actions for setting up the cache
       case 'cache':
           include_once './cache.php';
       break;
       case 'cache_check':
           $VIEW['action'] = 'cache';
           include_once './cache.php';
       break;
       case 'cache_success':
           header("Location: ./index.php?action=lib_path");
           die();
       break;

       // actions for setting path to library folder
       case 'lib_path':
           include_once './lib_path.php';
       break;
       case 'lib_path_check':
           $VIEW['action'] = 'lib_path';
           include_once './lib_path.php';
       break;
       case 'lib_path_success':
           header("Location: ./index.php?action=doc_path");
           die();
       break;

       // actions for specifying document path
       case 'doc_path':
           include_once './doc_path.php';
       break;
       case 'doc_path_check':
           $VIEW['action'] = 'doc_path';
           include_once './doc_path.php';
       break;
       case 'doc_path_success':
           if ($changeAppCredentials) {
               header("Location: ./index.php?action=app_credentials");
               die();
           } else if (isset($_SESSION['installation_info']['previous_version'])) {
               header("Location: ./index.php?action=patch");
               die();
           } else {
               header("Location: ./index.php?action=install");
               die();
           }
       break;

       // actions for specifying application credentials
       case 'app_credentials':
           include_once './app_credentials.php';
       break;
       case 'app_credentials_check':
           $VIEW['action'] = 'app_credentials';
           include_once './app_credentials.php';
       break;
       case 'app_credentials_success':
           if (isset($_SESSION['installation_info']['previous_version'])) {
               header("Location: ./index.php?action=patch");
               die();
           } else {
               header("Location: ./index.php?action=install");
               die();
           }
       break;

       // actions for patching previous versions of conjoon
       case 'patch':
           include_once './patch.php';
       break;
       case 'patch_check':
           include_once './patch.php';
       break;
       case 'patch_success':
           header("Location: ./index.php?action=install");
           die();
       break;

       // actions for finally installing conjoon
       case 'install':
           include_once './install.php';
       break;
       case 'install_process':
           $VIEW['action'] = 'install';
           include_once './install.php';
       break;
       case 'install_success':
           header("Location: ./index.php?action=finish");
           die();
       break;

       case 'finish':
           include_once './finish.php';
       break;

       // actions for the welcome screen
       default:
           include_once './welcome.php';
       break;
       case 'welcome_check':
           $VIEW['action'] = '';
           include_once './welcome.php';
       break;
       case 'welcome_success':
           header("Location: ./index.php?action=check");
           die();
       break;
   }

   $VIEW['content'] = ob_get_contents();
   ob_end_clean();


   include_once './view/templates/head.tpl';
   include_once './view/templates/body.tpl';
   include_once './view/templates/footer.tpl';