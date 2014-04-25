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
 * Library specific settings.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');


$LIB_SETTINGS = array();

function cacheSetup_getCacheDir($key, $value = null)
{
    if (isset($_SESSION['installation_info']['application.' . $key])) {
        return $_SESSION['installation_info']['application.' . $key];
    }

    $cacheSetup =& $_SESSION['setup_ini']['application'];

    if (strpos($key, 'cache_dir') !== false) {
        return rtrim($_SESSION['app_path'], '/')
               . '/'
               . rtrim($_SESSION['setup_ini']['app_path']['folder'], '/')
               . '/'
               . $cacheSetup[$key];
    } else {
        return $value;
    }
}


$applicationSetup =& $_SESSION['setup_ini']['application'];

if (isset($_SESSION['application'])) {
    $LIB_SETTINGS = $_SESSION['application'];
} else {
    $LIB_SETTINGS['htmlpurifier.preload_all'] = isset($_SESSION['installation_info']['application.htmlpurifier.preload_all'])
        ? $_SESSION['installation_info']['application.htmlpurifier.preload_all']
        : $_SESSION['setup_ini']['application']['htmlpurifier.preload_all'];


    $LIB_SETTINGS['htmlpurifier.use_cache'] = isset($_SESSION['installation_info']['application.htmlpurifier.use_cache'])
                                ? $_SESSION['installation_info']['application.htmlpurifier.use_cache']
                                : $_SESSION['setup_ini']['application']['htmlpurifier.use_cache'];

    foreach ($applicationSetup as $key => $value) {
        $LIB_SETTINGS[$key] = cacheSetup_getCacheDir($key, $value);
    }
}

if (isset($_POST['lib_settings_post']) && $_POST['lib_settings_post'] == "1") {

    $_SESSION['lib_settings_failed'] = false;
    $_SESSION['application'] = $applicationSetup;

    $_SESSION['application']['htmlpurifier.preload_all'] = $_POST['htmlpurifier_preload_all'];

    if(!$_POST['htmlpurifier_use_cache']) {
        $_SESSION['application']['htmlpurifier.use_cache'] = false;
        $_SESSION['application']['htmlpurifier.cache_dir'] =
            $_SESSION['setup_ini']['application']['htmlpurifier.cache_dir'];

        $_SESSION['lib_settings_failed'] = false;
    } else {

        $_SESSION['application']['htmlpurifier.use_cache'] = true;

        $tryCacheDir = $_POST['htmlpurifier_cache_dir'];

        if (trim($tryCacheDir) == "") {
            $tryCacheDir = cacheSetup_getCacheDir('htmlpurifier.cache_dir');
        }

        if (strpos($tryCacheDir, '/') !== 0 && strpos($tryCacheDir, ':') !== 1) {
            $tryCacheDir = rtrim($_SESSION['app_path'], '/')
                .'/'
                . rtrim($_SESSION['setup_ini']['app_path']['folder'], '/')
                . '/'
                . $tryCacheDir;
        }

        $dirCheck = conjoon_mkdir($tryCacheDir, true);

        $_SESSION['application']['htmlpurifier.cache_dir'] = $tryCacheDir;

        if ($dirCheck === false) {
            $_SESSION['application']['htmlpurifier.cache_dir.install_failed'] = true;
            $_SESSION['lib_settings_failed'] = true;
        }

    }

    if (!$_SESSION['lib_settings_failed']) {
        header("Location: ./?action=lib_settings_success");
        die();
    }

    $LIB_SETTINGS =& $_SESSION['application'];

}

include_once './view/lib_settings.tpl';
