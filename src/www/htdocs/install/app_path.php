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
 * Tests the path where the application folder was moved to.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');


$APPPATH = array();
$APPPATH['not_existing'] = false;
$APPPATH['is_readable']  = true;
$APPPATH['is_writable']  = true;
$APPPATH['not_allowed']  = false;

$_SESSION['app_path_failed'] = false;

$_SESSION['app_path'] = isset($_SESSION['app_path'])
                        ? $_SESSION['app_path']
                        : (isset($_SESSION['installation_info']['app_path'])
                           ? $_SESSION['installation_info']['app_path']
                           : ''
                        );

if (isset($_POST['app_path_post'])) {

    $appPath = str_replace("\\", "/", trim((string)$_POST['app_path']));

    if ($appPath == "") {
        $appPath = "../";
    }

    $_SESSION['app_path'] = $appPath;
    $appPath              = @realpath($appPath);

    if ($appPath === false) {
        $APPPATH['not_existing'] = true;
        $_SESSION['app_path_failed'] = true;
    } else {
        $appPath = str_replace("\\", "/", $appPath);
        $_SESSION['app_path'] = $appPath;
        $APPPATH['is_readable'] = @is_readable($appPath);
        $APPPATH['is_writable'] = @is_writable($appPath);
        if (!$APPPATH['is_readable'] || !$APPPATH['is_writable']) {
            $_SESSION['app_path_failed'] = true;
        }
    }

    // /home/user/apppath
    $cPath  = strtolower(rtrim($appPath, '/')) .'/';
    // /home/user/apppath/install/login
    $cPath2 = strtolower(rtrim(str_replace("\\", "/", getcwd()), '/')) . '/';

    if (strpos($cPath, $cPath2) === 0) {
        $APPPATH['not_allowed']      = true;
        $_SESSION['app_path_failed'] = true;
    }

    if (!$_SESSION['app_path_failed']) {
        header("Location: ./?action=app_path_success");
        die();
    }


}

include_once './view/app_path.tpl';