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
 * Install chunk_4
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

    InstallLogger::stdout(InstallLogger::logMessage("Moving and generating files..."));

    // move libs folders
    InstallLogger::stdout(InstallLogger::logMessage("Moving library folders"));
    $libFolders = explode(",", $_SESSION['setup_ini']['lib_path']['delete']);
    for ($i = 0, $len = count($libFolders); $i < $len; $i++) {
        $libFolders[$i] = trim($libFolders[$i]);
        conjoon_rmdir($_SESSION['lib_path'] . "/" . $libFolder . "/" . $libFolders[$i]);
        rmdir($_SESSION['lib_path'] . "/" . $libFolder . "/" . $libFolders[$i]);
        if (!file_exists($_SESSION['lib_path'] . "/" . $libFolder)) {
            mkdir($_SESSION['lib_path'] . "/" . $libFolder);
        }
        if (file_exists('./files/' . $libFolder . "/" . $libFolders[$i])) {
            rename(
                './files/' . $libFolder . "/" . $libFolders[$i],
                $_SESSION['lib_path'] . "/" . $libFolder . "/" . $libFolders[$i]
            );
        }
    }
    conjoon_copy('./htaccess.deny.txt',
        $_SESSION['lib_path'] . "/" . $libFolder . '/.htaccess');

    // replace $LIBRARY_PATH_BOOTSTRAP in index.php to enable autoloader
    // replace $LOCALE_DEFAULT_TIMEZONE in index.php for local.timezone
    // fallback
    InstallLogger::stdout(InstallLogger::logMessage("Enabling autoloader in index.php"));
    $indexFile = file_get_contents('../index.php');
    $indexFile = str_replace(
        array('$LIBRARY_PATH_BOOTSTRAP', '$LOCALE_DEFAULT_TIMEZONE'),
        array(
            "'".$_SESSION['lib_path'] . '/' . $libFolder ."'",
            "'".$_SESSION['locale_timezone_fallback']."'"
        ),
        $indexFile
    );
    file_put_contents('../index.php', $indexFile);

    // move application folders
    InstallLogger::stdout(InstallLogger::logMessage("Moving application folders"));
    $appFolders = explode(",", $_SESSION['setup_ini']['app_path']['delete']);
    for ($i = 0, $len = count($appFolders); $i < $len; $i++) {
        $appFolders[$i] = trim($appFolders[$i]);
        conjoon_rmdir($_SESSION['app_path'] . "/" . $appFolder . "/" . $appFolders[$i]);
        rmdir($_SESSION['app_path'] . "/" . $appFolder . "/" . $appFolders[$i]);
        if (!file_exists($_SESSION['app_path'] . "/" . $appFolder)) {
            mkdir($_SESSION['app_path'] . "/" . $appFolder);
        }
        if (file_exists('./files/' . $appFolder . "/" . $appFolders[$i])) {
            rename(
                './files/' . $appFolder . "/" . $appFolders[$i],
                $_SESSION['app_path'] . "/" . $appFolder . "/" . $appFolders[$i]
            );
        }
    }
    conjoon_copy('./htaccess.deny.txt', $_SESSION['app_path'] . "/" . $appFolder . '/.htaccess');


    // work on Doctrine-ORM files!
    InstallLogger::stdout(InstallLogger::logMessage("Creating ORM files"));
    conjoon_createOrmFiles(
        $_SESSION['app_path'] . "/" .
        $appFolder . "/" .
        $_SESSION['setup_ini']['application']['doctrine.orm.folder_name'],
        $_SESSION['db_table_prefix']
    );

    // work on HTML5 manifest files. Update them to use the configured base_url.
    InstallLogger::stdout(InstallLogger::logMessage("Updating HTML5 manifest files"));
    conjoon_updateHtml5ManifestFilesWithBasePath(
        $_SESSION['app_path'] . "/" . $appFolder, $_SESSION['doc_path']
    );

    // remove old htmlpurifier if needed
    InstallLogger::stdout(InstallLogger::logMessage("Updating HTMLPurifier functionality"));
    if (isset($_SESSION['installation_info']['application.htmlpurifier.use_cache'])
        && isset($_SESSION['installation_info']['application.htmlpurifier.cache_dir'])
        && file_exists($_SESSION['installation_info']['application.htmlpurifier.cache_dir'])) {
        @conjoon_rmdir($_SESSION['installation_info']['application.htmlpurifier.cache_dir']);
        @rmdir($_SESSION['installation_info']['application.htmlpurifier.cache_dir']);
    }
    // ... and create new dir if necessary
    if ($_SESSION['application']['htmlpurifier.use_cache']
        && $_SESSION['application']['htmlpurifier.cache_dir']) {
        conjoon_mkdir($_SESSION['application']['htmlpurifier.cache_dir']);
        conjoon_copy('./htaccess.deny.txt',
            $_SESSION['application']['htmlpurifier.cache_dir'] . '/.htaccess');
    }

    // process doctrine cache directories
    InstallLogger::stdout(InstallLogger::logMessage("Processing Doctrine cache directories"));
    $doctrineCacheConfigKeys = array('query_cache', 'metadata_cache');
    foreach ($doctrineCacheConfigKeys as $doctrineCacheConfigKey) {
        // remove old doctrine cache if needed
        if (isset($_SESSION['installation_info']['application.doctrine.cache.'.$doctrineCacheConfigKey.'.type']) &&
            $_SESSION['installation_info']['application.doctrine.cache.'.$doctrineCacheConfigKey.'.type'] == 'file' &&
            isset($_SESSION['installation_info']['application.doctrine.cache.'.$doctrineCacheConfigKey.'.dir']) &&
            file_exists($_SESSION['installation_info']['application.doctrine.cache.'.$doctrineCacheConfigKey.'.dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['application.doctrine.cache.'.$doctrineCacheConfigKey.'.dir']);
            @rmdir($_SESSION['installation_info']['application.doctrine.cache.'.$doctrineCacheConfigKey.'.dir']);
        }
        // ... and create new dir if necessary
        if ($_SESSION['application']['doctrine.cache.'.$doctrineCacheConfigKey.'.type'] == 'file'
            && $_SESSION['application']['doctrine.cache.'.$doctrineCacheConfigKey.'.dir']) {
            conjoon_mkdir($_SESSION['application']['doctrine.cache.'.$doctrineCacheConfigKey.'.dir']);
            conjoon_copy('./htaccess.deny.txt',
                $_SESSION['application']['doctrine.cache.'.$doctrineCacheConfigKey.'.dir'] . '/.htaccess');
        }
    }

    // process file related directory
    // ... and create new dir if necessary
    // dont remove previous directories snce it might be needed to
    // re-store previous files handled by the dirs configured
    // for previous installations
    InstallLogger::stdout(InstallLogger::logMessage("Processing file system functionality"));
    if ($_SESSION['files']['storage.filesystem.enabled']
        && $_SESSION['files']['storage.filesystem.dir']) {
        conjoon_mkdir($_SESSION['files']['storage.filesystem.dir']);
        conjoon_copy('./htaccess.deny.txt',
            $_SESSION['files']['storage.filesystem.dir'] . '/.htaccess');
    }

    echo "<script type=\"text/javascript\">this.location.href=\"./index.php?action=install_chunk_5\"</script>";