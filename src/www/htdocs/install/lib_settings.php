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

$DOCTRINE_CACHE_TYPES = array(
                            'query_cache' => array(
                                'name' => 'Query Cache'
                            ),
                            'metadata_cache' => array(
                                'name' => 'Metadata Cache'
                            ));

$DOCTRINE_CACHE_EXTENSIONS = array('apc', 'memcache', 'memcached', 'file');

function cacheSetup_getCacheDir($key, $value = null)
{
    if (isset($_SESSION['installation_info']['application.' . $key])
    && !empty($_SESSION['installation_info']['application.' . $key])) {
        return $_SESSION['installation_info']['application.' . $key];
    }

    $cacheSetup =& $_SESSION['setup_ini']['application'];

    if (strpos($key, 'cache_dir') !== false ||
        strpos($key, '.dir') !== false) {
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

    $getMyKeys = array(
        'htmlpurifier.preload_all',
        'htmlpurifier.use_cache',
        'doctrine.cache.enabled',
        'doctrine.cache.query_cache.enabled',
        'doctrine.cache.query_cache.type',
        'doctrine.cache.query_cache.dir',
        'doctrine.cache.metadata_cache.enabled',
        'doctrine.cache.metadata_cache.type',
        'doctrine.cache.metadata_cache.dir',
    );

    foreach ($getMyKeys as $heresYourKey) {
        $LIB_SETTINGS[$heresYourKey] = isset($_SESSION['installation_info']['application.' . $heresYourKey])
            && !empty($_SESSION['installation_info']['application.' . $heresYourKey])
            ? $_SESSION['installation_info']['application.' . $heresYourKey]
            : $_SESSION['setup_ini']['application'][$heresYourKey];
    }

    foreach ($applicationSetup as $key => $value) {
        $LIB_SETTINGS[$key] = cacheSetup_getCacheDir($key, $value);
    }
}

if (isset($_POST['lib_settings_post']) && $_POST['lib_settings_post'] == "1") {

    $_SESSION['lib_settings_failed'] = false;
    $_SESSION['application'] = $applicationSetup;

// +-----------------------+
// | htmlpurifier section  |
// +-----------------------+
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


// +-----------------------+
// |   doctrine section    |
// +-----------------------+

    if(!$_POST['doctrine_cache_enabled']) {
        $_SESSION['application']['doctrine.cache.enabled'] = false;
        $_SESSION['application']['doctrine.cache.query_cache.dir'] =
            $_SESSION['setup_ini']['application']['doctrine.cache.query_cache.dir'];
        $_SESSION['application']['doctrine.cache.metadata_cache.dir'] =
            $_SESSION['setup_ini']['application']['doctrine.cache.metadata_cache.dir'];

        $_SESSION['lib_settings_failed'] = false;
    } else {

        $_SESSION['application']['doctrine.cache.enabled'] = true;

        foreach ($DOCTRINE_CACHE_TYPES as $doctrineCacheKey => $doctrineCacheValues) {
            $_SESSION['application']['doctrine.cache.' . $doctrineCacheKey . '.enabled'] =
                $_POST['doctrine_cache_' . $doctrineCacheKey . '_enabled'] == true;

            if ($_SESSION['application']['doctrine.cache.' . $doctrineCacheKey . '.enabled']) {

                $_SESSION['application']['doctrine.cache.' . $doctrineCacheKey . '.type'] =
                    $_POST['doctrine_cache_' . $doctrineCacheKey . '_type'];

                if ($_SESSION['application']['doctrine.cache.' . $doctrineCacheKey . '.type'] == 'file') {

                    // check directories
                    $tryCacheDir = $_POST['doctrine_cache_' . $doctrineCacheKey . '_dir'];
                    if (trim($tryCacheDir) == "") {
                        $tryCacheDir = cacheSetup_getCacheDir(
                            'doctrine.cache.' . $doctrineCacheKey . '.dir');
                    }

                    if (strpos($tryCacheDir, '/') !== 0 && strpos($tryCacheDir, ':') !== 1) {
                        $tryCacheDir = rtrim($_SESSION['app_path'], '/')
                            .'/'
                            . rtrim($_SESSION['setup_ini']['app_path']['folder'], '/')
                            . '/'
                            . $tryCacheDir;
                    }

                    $dirCheck = conjoon_mkdir($tryCacheDir, true);

                    $_SESSION['application']['doctrine.cache.' . $doctrineCacheKey . '.dir'] = $tryCacheDir;

                    if ($dirCheck === false) {
                        $_SESSION['application']['doctrine.cache.' . $doctrineCacheKey . '.dir.install_failed'] = true;
                        $_SESSION['lib_settings_failed'] = true;
                    }
                }


            } else {
                $_SESSION['application']['doctrine.cache.' . $doctrineCacheKey . '.dir'] =
                    $_SESSION['setup_ini']['application']['doctrine.cache.' . $doctrineCacheKey . '.cache_dir'];

            }

        }
    }

    if (!$_SESSION['lib_settings_failed']) {
        header("Location: ./?action=lib_settings_success");
        die();
    }

    $LIB_SETTINGS =& $_SESSION['application'];

}

include_once './view/lib_settings.tpl';
