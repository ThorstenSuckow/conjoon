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
 * Tests the path where the application folder was moved to.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');


$CACHE = array();

$cacheSetup =& $_SESSION['setup_ini']['cache'];

if (isset($_SESSION['cache'])) {
    $CACHE = $_SESSION['cache'];
} else {

    $CACHE['default.caching'] = isset($_SESSION['installation_info']['cache.default.caching'])
                                ? $_SESSION['installation_info']['cache.default.caching']
                                : $_SESSION['setup_ini']['cache']['default.caching'];

    foreach ($cacheSetup as $key => $value) {
        $CACHE[$key] = conjoon_cacheSetup_getCacheDir($key, 'cache', $value);
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
                    $tryCacheDir = conjoon_cacheSetup_getCacheDir(
                        $namespace . '.backend.cache_dir', 'cache');
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