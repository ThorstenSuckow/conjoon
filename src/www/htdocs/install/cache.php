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
 * Tests the path where the application folder was moved to.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */



$CACHE = array();

function cacheSetup_getCacheDir($key, $value = null)
{
    if (isset($_SESSION['installation_info']['cache.' . $key])) {
        return $_SESSION['installation_info']['cache.' . $key];
    }

    $cacheSetup =& $_SESSION['setup_ini']['cache'];

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


$cacheSetup =& $_SESSION['setup_ini']['cache'];

if (isset($_SESSION['cache'])) {
    $CACHE = $_SESSION['cache'];
} else {

    $CACHE['default.caching'] = isset($_SESSION['installation_info']['cache.default.caching'])
                                ? $_SESSION['installation_info']['cache.default.caching']
                                : $_SESSION['setup_ini']['cache']['default.caching'];

    foreach ($cacheSetup as $key => $value) {
        $CACHE[$key] = cacheSetup_getCacheDir($key, $value);
    }
}

if (isset($_POST['cache_post']) && $_POST['cache_post'] == "1") {
    $_SESSION['cache_failed'] = false;
    $_SESSION['cache'] = $cacheSetup;

    if(!$_POST['default_caching']) {

        foreach($_SESSION['cache'] as $m => $r) {
            $_SESSION['cache'][$m] = "";
        }

        $_SESSION['cache']['default.caching'] = false;
        $_SESSION['cache_failed'] = false;
    } else {

        $_SESSION['cache']['default.caching'] = true;

        $POST = $_POST;

        foreach ($POST as $key => $value) {

            if ($key === 'default_caching' || $key === 'cache_post') {
                continue;
            }

            $ns = explode('_', $key);

            $postedNs  = $ns[0] . '_' .$ns[1];
            $namespace = $ns[0] . '.' . conjoon_underscoreString($ns[1]);
            // org : feed.itemList
            // ns  : feed.item_list

            if ($POST[str_replace('.', '_', $postedNs) . '_caching']) {
                $_SESSION['cache'][$namespace . '.caching'] = true;

                $tryCacheDir = $POST[str_replace('.', '_', $postedNs) . '_backend_cache_dir'];

                if (trim($tryCacheDir) == "") {
                    $tryCacheDir = cacheSetup_getCacheDir($namespace . '.backend.cache_dir');
                }

                if (strpos($tryCacheDir, '/') !== 0 && strpos($tryCacheDir, ':') !== 1) {
                    $tryCacheDir = rtrim($_SESSION['app_path'], '/')
                                   .'/'
                                   . rtrim($_SESSION['setup_ini']['app_path']['folder'], '/')
                                   . '/'
                                   . $tryCacheDir;
                }

                $dirCheck = conjoon_mkdir($tryCacheDir, true);

                $_SESSION['cache'][$namespace . '.backend.cache_dir'] = $tryCacheDir;

                if ($dirCheck === false) {
                    $_SESSION['cache'][$namespace.'.install_failed'] = true;
                    $_SESSION['cache_failed'] = true;
                } else {
                    $_SESSION['cache'][$namespace . '.backend.cache_dir'] = $tryCacheDir;
                }


            } else {
                $_SESSION['cache'][$namespace . '.caching'] = false;
                $_SESSION['cache'][$namespace . '.backend.cache_dir'] =
                    $POST[str_replace('.', '_', $postedNs) . '_backend_cache_dir'];
            }

        }

    }

    if (!$_SESSION['cache_failed']) {
        header("Location: ./?action=cache_success");
        die();
    }

    $CACHE =& $_SESSION['cache'];

}

include_once './view/cache.tpl';