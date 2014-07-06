<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
 * Will sum up all the installation data and install conjoon upon a post request.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');

$INSTALL = array();

$INSTALL['include_path']['ini'] = rtrim($_SESSION['lib_path'], '/')
                                  . '/'
                                  . $_SESSION['setup_ini']['lib_path']['folder'];

$INSTALL['include_path']['delete'] = explode(",", $_SESSION['setup_ini']['lib_path']['delete']);

$INSTALL['app_path']['full']   = rtrim($_SESSION['app_path'], '/')
                                  . '/'
                                  . $_SESSION['setup_ini']['app_path']['folder'];
$INSTALL['app_path']['delete'] = explode(",", $_SESSION['setup_ini']['app_path']['delete']);

$INSTALL['include_path']['delete_warning'] = false;
$INSTALL['app_path']['delete_warning']     = false;

$fAppPaths = $INSTALL['app_path']['delete'];

$INSTALL['app_path']['delete'] = array();
for ($i = 0, $len = count($fAppPaths); $i < $len; $i++) {
    if (file_exists($INSTALL['app_path']['full'] .'/' . $fAppPaths[$i])) {
        $INSTALL['app_path']['delete'][] = $fAppPaths[$i];
    }
}

if (file_exists($INSTALL['app_path']['full'])) {
    $INSTALL['app_path']['delete_warning'] = true;
}

if (count($INSTALL['app_path']['delete']) == 0) {
    $INSTALL['app_path']['delete_warning'] = false;
}

if (file_exists($INSTALL['include_path']['ini'])) {
    $INSTALL['include_path']['delete_warning'] = true;
}

$INSTALL['IMREMOVING'] = array(
    '_configCache' => file_exists('../_configCache'),
    'js'           => file_exists('../js')
);

$INSTALL['CACHE_REMOVE'] = array(
    'WARNING' => false,
    'FILES'   => array()
);

if (isset($_SESSION['installation_info'])) {
    if (isset($_SESSION['installation_info']['cache.default.caching'])
        && $_SESSION['installation_info']['cache.default.caching']) {
        $INSTALL['CACHE_REMOVE']['WARNING'] = true;

        if (isset($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.db.metadata.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.email.message.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.email.message.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.email.message.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.email.accounts.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.email.folders_root_type.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.email.folders_root_type.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.email.folders_root_type.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.feed.item.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.item.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.feed.item.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.feed.reader.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.feed.account.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.account.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.feed.account.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir'];
        }
    }
}


if (isset($_POST['install_post'])) {


    $libFolder = $_SESSION['setup_ini']['lib_path']['folder'];
    $appFolder = $_SESSION['setup_ini']['app_path']['folder'];


    // delete folders from a previous installation
    if ($INSTALL['IMREMOVING']['js']) {
        conjoon_rmdir('../js');
        rmdir('../js');
    }
    if ($INSTALL['IMREMOVING']['_configCache']) {
        conjoon_rmdir('../_configCache');
        rmdir('../_configCache');
    }

    // move js folder to htdocs
    rename('./files/js', '../js');

    // move _configCache to htdocs
    rename('./files/_configCache', '../_configCache');
    conjoon_copy('./htaccess.deny.txt', '../_configCache/.htaccess');

    header("Location: ./?action=install_success");
    die();
}

include_once './view/install.tpl';
