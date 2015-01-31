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


$applicationSetup =& $_SESSION['setup_ini']['application'];

if (isset($_SESSION['application'])) {
    $LIB_SETTINGS = $_SESSION['application'];
} else {

    $getMyKeys = array(
        'htmlpurifier.preload_all' => array(),
        'htmlpurifier.use_cache' => array(),
        'htmlpurifier.cache_dir' => array(),
        'doctrine.cache.enabled' => array(),
        'doctrine.cache.query_cache.enabled' => array(),
        'doctrine.cache.query_cache.type' => array('allowEmpty' => false),
        'doctrine.cache.query_cache.dir' => array(),
        'doctrine.cache.metadata_cache.enabled' => array(),
        'doctrine.cache.metadata_cache.type' => array('allowEmpty' => false),
        'doctrine.cache.metadata_cache.dir' => array(),
    );

    foreach ($getMyKeys as $heresYourKey => $heresYourValue) {

        // gather default value!
        $LIB_SETTINGS[$heresYourKey] = conjoon_cacheSetup_getConfigurationDefaultValue(
            $heresYourKey, 'application', $getMyKeys[$heresYourKey]
        );

        // adjust the value if necessary
        if (strpos($heresYourKey, 'cache_dir') !== false ||
            strpos($heresYourKey, '.dir') !== false) {
            $LIB_SETTINGS[$heresYourKey] = conjoon_cacheSetup_assembleDir(
                $heresYourKey, 'application', $LIB_SETTINGS[$heresYourKey]
            );
        }

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
            $tryCacheDir = conjoon_cacheSetup_getCacheDir(
                'htmlpurifier.cache_dir', 'application');
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
                        $tryCacheDir = conjoon_cacheSetup_getCacheDir(
                            'doctrine.cache.' . $doctrineCacheKey . '.dir', 'application');
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
