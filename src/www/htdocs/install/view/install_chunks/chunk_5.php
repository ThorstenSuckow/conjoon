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
 * $Author: T. Suckow $
 * $Id: cache.php 1985 2014-07-05 13:00:08Z T. Suckow $
 * $Date: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $Revision: 1985 $
 * $LastChangedDate: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $LastChangedBy: T. Suckow $
 * $URL: http://svn.conjoon.org/trunk/src/www/htdocs/install/cache.php $
 */

/**
 * Install chunk_5
 *
 * Takes care of file operations
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');

    $libFolder = $_SESSION['setup_ini']['lib_path']['folder'];
    $appFolder = $_SESSION['setup_ini']['app_path']['folder'];

    InstallLogger::getInstance($_SESSION['install_process']['INSTALL_LOGGER']);

    InstallLogger::stdout(InstallLogger::logMessage("Updating cache system..."), true);

    // remove old cache folders
    if (isset($_SESSION['installation_info']['cache.default.caching'])
        && $_SESSION['installation_info']['cache.default.caching']) {

        if (isset($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.email.message.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.email.message.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.email.message.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.email.message.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.email.folders_root_type.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.email.folders_root_type.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.email.folders_root_type.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.email.folders_root_type.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.feed.item.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.item.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.feed.item.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.feed.item.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.feed.account.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.account.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.feed.account.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.feed.account.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir']);
        }
    }

    // create caching folders
    if ($_SESSION['cache']['default.caching']) {

        if ($_SESSION['cache']['db.metadata.caching']) {
            conjoon_mkdir($_SESSION['cache']['db.metadata.backend.cache_dir']);
            conjoon_copy('./htaccess.deny.txt',
                $_SESSION['cache']['db.metadata.backend.cache_dir'] . '/.htaccess');
        }

        if ($_SESSION['cache']['email.message.caching']) {
            conjoon_mkdir($_SESSION['cache']['email.message.backend.cache_dir']);
            conjoon_copy('./htaccess.deny.txt',
                $_SESSION['cache']['email.message.backend.cache_dir'] . '/.htaccess');
        }

        if ($_SESSION['cache']['email.accounts.caching']) {
            conjoon_mkdir($_SESSION['cache']['email.accounts.backend.cache_dir']);
            conjoon_copy('./htaccess.deny.txt',
                $_SESSION['cache']['email.accounts.backend.cache_dir'] . '/.htaccess');
        }

        if ($_SESSION['cache']['email.folders_root_type.caching']) {
            conjoon_mkdir($_SESSION['cache']['email.folders_root_type.backend.cache_dir']);
            conjoon_copy('./htaccess.deny.txt',
                $_SESSION['cache']['email.folders_root_type.backend.cache_dir'] . '/.htaccess');
        }

        if ($_SESSION['cache']['feed.item.caching']) {
            conjoon_mkdir($_SESSION['cache']['feed.item.backend.cache_dir']);
            conjoon_copy('./htaccess.deny.txt',
                $_SESSION['cache']['feed.item.backend.cache_dir'] . '/.htaccess');
        }

        if ($_SESSION['cache']['feed.item_list.caching']) {
            conjoon_mkdir($_SESSION['cache']['feed.item_list.backend.cache_dir']);
            conjoon_copy('./htaccess.deny.txt',
                $_SESSION['cache']['feed.item_list.backend.cache_dir'] . '/.htaccess');
        }

        if ($_SESSION['cache']['feed.reader.caching']) {
            conjoon_mkdir($_SESSION['cache']['feed.reader.backend.cache_dir']);
            conjoon_copy('./htaccess.deny.txt',
                $_SESSION['cache']['feed.reader.backend.cache_dir'] . '/.htaccess');
        }

        if ($_SESSION['cache']['feed.account.caching']) {
            conjoon_mkdir($_SESSION['cache']['feed.account.backend.cache_dir']);
            conjoon_copy('./htaccess.deny.txt',
                $_SESSION['cache']['feed.account.backend.cache_dir'] . '/.htaccess');
        }

        if ($_SESSION['cache']['feed.account_list.caching']) {
            conjoon_mkdir($_SESSION['cache']['feed.account_list.backend.cache_dir']);
            conjoon_copy('./htaccess.deny.txt',
                $_SESSION['cache']['feed.account_list.backend.cache_dir'] . '/.htaccess');
        }

        if ($_SESSION['cache']['twitter.accounts.caching']) {
            conjoon_mkdir($_SESSION['cache']['twitter.accounts.backend.cache_dir']);
            conjoon_copy('./htaccess.deny.txt',
                $_SESSION['cache']['twitter.accounts.backend.cache_dir'] . '/.htaccess');
        }
    }

    echo "<script type=\"text/javascript\">this.location.href=\"./index.php?action=install_chunk_6\"</script>";