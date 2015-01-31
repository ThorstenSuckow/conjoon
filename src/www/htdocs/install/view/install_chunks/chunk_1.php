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
 * Install chunk_1
 *
 * Takes care of creating config/installation info files
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');

    $libFolder = $_SESSION['setup_ini']['lib_path']['folder'];

    InstallLogger::getInstance($_SESSION['install_process']['INSTALL_LOGGER']);

    InstallLogger::stdout(InstallLogger::logMessage("Generating htaccess"), true);

    // proceed installation. First of, generate .htaccess
    $htaccess = file_get_contents('./htaccess.template');
    $htaccess = str_replace("{REWRITE_BASE}", $_SESSION['doc_path'], $htaccess);
    file_put_contents('../.htaccess', $htaccess);
    $htaccess = "";


    // generate config.ini.php
    InstallLogger::stdout(InstallLogger::logMessage("Generating config.ini.php"), true);
    $configini = file_get_contents('./config.ini.php.template');

    if ($_SESSION['add_include_path']) {
        $configini = str_replace(
            "{INCLUDE_PATH}",
            $_SESSION['lib_path'] . "/" . $libFolder .
            /**
             * @ticket CN-839
             */
            ':' .
            $_SESSION['lib_path'] . "/" . $libFolder .
            '/HTMLPurifier/library',
            $configini
        );
    } else {
        $configini = str_replace("{INCLUDE_PATH}", "", $configini);
    }

    $configini = str_replace(
        "{APPLICATION_PATH}",
        $_SESSION['app_path'] . '/' . $_SESSION['setup_ini']['app_path']['folder'],
        $configini
    );

    InstallLogger::stdout(InstallLogger::logMessage("Adding database information to config.ini.php"), true);
    $configini = str_replace("{BASE_URL}", $_SESSION['doc_path'], $configini);
    $configini = str_replace("{EDITION}", '"' . $_SESSION['edition']. '"', $configini);
    $configini = str_replace("{DATABASE.ADAPTER}", $_SESSION['db_adapter'], $configini);
    $configini = str_replace("{DATABASE.HOST}", $_SESSION['db_host'], $configini);
    $configini = str_replace("{DATABASE.PORT}", $_SESSION['db_port'], $configini);
    $configini = str_replace("{DATABASE.USER}", $_SESSION['db_user'], $configini);
    $configini = str_replace("{DATABASE.TABLE.PREFIX}", $_SESSION['db_table_prefix'], $configini);
    $configini = str_replace("{DATABASE.PASSWORD}", $_SESSION['db_password'], $configini);
    $configini = str_replace("{DATABASE.DATABASE}", $_SESSION['db'], $configini);
    $configini = str_replace("{DATABASE_MAX_ALLOWED_PACKET}", $_SESSION['max_allowed_packet'], $configini);

    // localization
    InstallLogger::stdout(InstallLogger::logMessage("Adding localization information to config.ini.php"), true);
    $configini = str_replace("{LOCALE.DATE.TIMEZONE}", $_SESSION['locale_timezone_default'], $configini);

    // caching
    InstallLogger::stdout(InstallLogger::logMessage("Adding database caching to config.ini.php"), true);
    $configini = str_replace("{CACHE.DEFAULT.CACHING}", $_SESSION['cache']['default.caching'] ? '1' : '0', $configini);

    $configini = str_replace("{CACHE.DB.METADATA.CACHING}", $_SESSION['cache']['db.metadata.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.DB.METADATA.BACKEND.CACHE_DIR}",
        $_SESSION['cache']['db.metadata.caching']
            ? $_SESSION['cache']['db.metadata.backend.cache_dir']
            : "",
        $configini);

    $configini = str_replace("{CACHE.EMAIL.MESSAGE.CACHING}", $_SESSION['cache']['email.message.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.EMAIL.MESSAGE.BACKEND.CACHE_DIR}",
        $_SESSION['cache']['email.message.caching']
            ? $_SESSION['cache']['email.message.backend.cache_dir']
            : "",
        $configini);

    $configini = str_replace("{CACHE.EMAIL.ACCOUNTS.CACHING}", $_SESSION['cache']['email.accounts.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.EMAIL.ACCOUNTS.BACKEND.CACHE_DIR}",
        $_SESSION['cache']['email.accounts.caching']
            ? $_SESSION['cache']['email.accounts.backend.cache_dir']
            : "",
        $configini);

    $configini = str_replace("{CACHE.EMAIL.FOLDERS_ROOT_TYPE.CACHING}", $_SESSION['cache']['email.folders_root_type.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.EMAIL.FOLDERS_ROOT_TYPE.BACKEND.CACHE_DIR}",
        $_SESSION['cache']['email.folders_root_type.caching']
            ? $_SESSION['cache']['email.folders_root_type.backend.cache_dir']
            : "",
        $configini);

    $configini = str_replace("{CACHE.FEED.ITEM.CACHING}", $_SESSION['cache']['feed.item.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.FEED.ITEM.BACKEND.CACHE_DIR}",
        $_SESSION['cache']['feed.item.caching']
            ? $_SESSION['cache']['feed.item.backend.cache_dir']
            : "",
        $configini);

    $configini = str_replace("{CACHE.FEED.ITEM_LIST.CACHING}", $_SESSION['cache']['feed.item_list.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.FEED.ITEM_LIST.BACKEND.CACHE_DIR}",
        $_SESSION['cache']['feed.item_list.caching']
            ? $_SESSION['cache']['feed.item_list.backend.cache_dir']
            : "",
        $configini);

    $configini = str_replace("{CACHE.FEED.READER.CACHING}", $_SESSION['cache']['feed.reader.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.FEED.READER.BACKEND.CACHE_DIR}",
        $_SESSION['cache']['feed.reader.caching']
            ? $_SESSION['cache']['feed.reader.backend.cache_dir']
            : "",
        $configini);

    $configini = str_replace("{CACHE.FEED.ACCOUNT.CACHING}", $_SESSION['cache']['feed.account.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.FEED.ACCOUNT.BACKEND.CACHE_DIR}",
        $_SESSION['cache']['feed.account.caching']
            ? $_SESSION['cache']['feed.account.backend.cache_dir']
            : "",
        $configini);

    $configini = str_replace("{CACHE.FEED.ACCOUNT_LIST.CACHING}", $_SESSION['cache']['feed.account_list.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.FEED.ACCOUNT_LIST.BACKEND.CACHE_DIR}",
        $_SESSION['cache']['feed.account_list.caching']
            ? $_SESSION['cache']['feed.account_list.backend.cache_dir']
            : "",
        $configini);

    $configini = str_replace("{CACHE.TWITTER.ACCOUNTS.CACHING}", $_SESSION['cache']['twitter.accounts.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.TWITTER.ACCOUNTS.BACKEND.CACHE_DIR}",
        $_SESSION['cache']['twitter.accounts.caching']
            ? $_SESSION['cache']['twitter.accounts.backend.cache_dir']
            : "",
        $configini);

    // Htmlpurifier settings
    InstallLogger::stdout(InstallLogger::logMessage("Adding htmlpurifier information to config.ini.php"), true);
    $configini = str_replace(
        '{HTMLPURIFIER.PRELOAD_ALL}',
        $_SESSION['application']['htmlpurifier.preload_all'] ? '1' : '0',
        $configini
    );
    $configini = str_replace(
        '{HTMLPURIFIER.USE_CACHE}',
        $_SESSION['application']['htmlpurifier.use_cache'] ? '1' : '0',
        $configini
    );
    $configini = str_replace(
        '{HTMLPURIFIER.CACHE_DIR}',
        $_SESSION['application']['htmlpurifier.use_cache']
        && $_SESSION['application']['htmlpurifier.cache_dir']
            ? $_SESSION['application']['htmlpurifier.cache_dir']
            : "",
        $configini
    );

    // conjoon settings
    // file related settings
    InstallLogger::stdout(InstallLogger::logMessage("Adding file related information to config.ini.php"), true);
    $configini = str_replace(
        '{FILES.UPLOAD.MAX_SIZE}',
        $_SESSION['files']['upload.max_size'],
        $configini
    );
    $configini = str_replace(
        '{FILES.STORAGE.FILESYSTEM.ENABLED}',
        $_SESSION['files']['storage.filesystem.enabled'] ? '1' : '0',
        $configini
    );
    $configini = str_replace(
        '{FILES.STORAGE.FILESYSTEM.DIR}',
        $_SESSION['files']['storage.filesystem.enabled']
        && $_SESSION['files']['storage.filesystem.dir']
            ? $_SESSION['files']['storage.filesystem.dir']
            : "",
        $configini
    );

    // Doctrine settings
    InstallLogger::stdout(InstallLogger::logMessage("Adding doctrine information to config.ini.php"), true);
    $configini = str_replace(
        '{DOCTRINE.CACHE.ENABLED}',
        $_SESSION['application']['doctrine.cache.enabled'] ? '1' : '0',
        $configini
    );
    $doctrineCacheConfigKeys = array('QUERY_CACHE', 'METADATA_CACHE');
    foreach ($doctrineCacheConfigKeys as $doctrineCacheConfigKey) {
        $configini = str_replace(
            '{DOCTRINE.CACHE.' . $doctrineCacheConfigKey . '.ENABLED}',
            $_SESSION['application']['doctrine.cache.enabled'] &&
            $_SESSION['application']['doctrine.cache.' . strtolower($doctrineCacheConfigKey) . '.enabled'] ? '1' : '0',
            $configini
        );
        $configini = str_replace(
            '{DOCTRINE.CACHE.' . $doctrineCacheConfigKey . '.TYPE}',
            $_SESSION['application']['doctrine.cache.' . strtolower($doctrineCacheConfigKey) . '.enabled']
            && $_SESSION['application']['doctrine.cache.enabled']
                ? $_SESSION['application']['doctrine.cache.' . strtolower($doctrineCacheConfigKey) . '.type']
                : '',
            $configini
        );
        $configini = str_replace(
            '{DOCTRINE.CACHE.' . $doctrineCacheConfigKey . '.DIR}',
            ($_SESSION['application']['doctrine.cache.' . strtolower($doctrineCacheConfigKey) . '.enabled']
            && $_SESSION['application']['doctrine.cache.enabled']
            && $_SESSION['application']['doctrine.cache.' . strtolower($doctrineCacheConfigKey) . '.type'] == 'file'
                ? $_SESSION['application']['doctrine.cache.' . strtolower($doctrineCacheConfigKey) . '.dir']
                : ''),
            $configini
        );
    }


    // overwrite the file completely, even if it still exists from a previous
    // installation!
    InstallLogger::stdout(InstallLogger::logMessage("Writing config.ini"), true);
    file_put_contents('../config.ini.php', $configini);
    $configini = "";


    // generate and update install.info.php
    InstallLogger::stdout(InstallLogger::logMessage("Writing installation.info.php"), true);
    if (!file_exists('../installation.info.php')) {
        $installationinfo = file_get_contents('./installation.info.php.template');
    } else {
        $installationinfo = file_get_contents('../installation.info.php');
    }
    $installationinfo .= "\n
        // generated by conjoon V".$_SESSION['current_version']."
        // on ".date("m-d-Y H:i:s", time())."
        \$INSTALLATION_INFO[] = array(
            'locale_timezone_default'  => '"
    .$_SESSION['locale_timezone_default']."',
            'locale_timezone_fallback' => '"
    .$_SESSION['locale_timezone_fallback']."',
            'support_key'        => '".$_SESSION['support_key']."',
            'version'            => '".$_SESSION['current_version']."',
            'date'               => '".time()."',
            'db_host'            => '".$_SESSION['db_host']."',
            'db_adapter'         => '".$_SESSION['db_adapter']."',
            'db'                 => '".$_SESSION['db']."',
            'db_port'            => '".$_SESSION['db_port']."',
            'db_user'            => '".$_SESSION['db_user']."',
            'db_table_prefix'    => '".$_SESSION['db_table_prefix']."',
            'edition'            => '".$_SESSION['edition']."',
            'max_allowed_packet' => '".$_SESSION['max_allowed_packet']."',
            'app_path'           => '".$_SESSION['app_path']."',
            'lib_path'           => '".$_SESSION['lib_path']."',
            'add_include_path'   => '".$_SESSION['add_include_path']."',
            'doc_path'           => '".$_SESSION['doc_path']."',
            'applied_patches'    => array(".(empty($_SESSION['applied_patches']) ? "" : "'").implode(
        "','",
            (isset($_SESSION['applied_patches']) && is_array($_SESSION['applied_patches'])
                ? array_values($_SESSION['applied_patches']) : array())
    ).(empty($_SESSION['applied_patches']) ? "" : "'")."),
            'ignored_patches'    => array(".(empty($_SESSION['ignored_patches']) ? "" : "'").implode(
        "','",
            (isset($_SESSION['ignored_patches']) && is_array($_SESSION['ignored_patches']) ? array_values($_SESSION['ignored_patches']) : array())
    ).(empty($_SESSION['ignored_patches']) ? "" : "'")."),


            ".($_SESSION['cache']['default.caching']
        ? "

              'cache.default.caching' => 1,

              'cache.db.metadata.caching' => ".($_SESSION['cache']['db.metadata.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['db.metadata.caching'] ? "'cache.db.metadata.backend.cache_dir' => '".$_SESSION['cache']['db.metadata.backend.cache_dir']."'," : "")."

              'cache.email.message.caching' => ".($_SESSION['cache']['email.message.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['email.message.caching'] ? "'cache.email.message.backend.cache_dir' => '".$_SESSION['cache']['email.message.backend.cache_dir']."'," : "")."

              'cache.email.accounts.caching' => ".($_SESSION['cache']['email.accounts.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['email.accounts.caching'] ? "'cache.email.accounts.backend.cache_dir' => '".$_SESSION['cache']['email.accounts.backend.cache_dir']."'," : "")."

              'cache.email.folders_root_type.caching' => ".($_SESSION['cache']['email.folders_root_type.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['email.folders_root_type.caching'] ? "'cache.email.folders_root_type.backend.cache_dir' => '".$_SESSION['cache']['email.folders_root_type.backend.cache_dir']."'," : "")."

              'cache.feed.item.caching' => ".($_SESSION['cache']['feed.item.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['feed.item.caching'] ? "'cache.feed.item.backend.cache_dir' => '".$_SESSION['cache']['feed.item.backend.cache_dir']."'," : "")."

              'cache.feed.item_list.caching' => ".($_SESSION['cache']['feed.item_list.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['feed.item_list.caching'] ? "'cache.feed.item_list.backend.cache_dir' => '".$_SESSION['cache']['feed.item_list.backend.cache_dir']."'," : "")."

              'cache.feed.reader.caching' => ".($_SESSION['cache']['feed.reader.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['feed.reader.caching'] ? "'cache.feed.reader.backend.cache_dir' => '".$_SESSION['cache']['feed.reader.backend.cache_dir']."'," : "")."

              'cache.feed.account.caching' => ".($_SESSION['cache']['feed.account.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['feed.account.caching'] ? "'cache.feed.account.backend.cache_dir' => '".$_SESSION['cache']['feed.account.backend.cache_dir']."'," : "")."

              'cache.feed.account_list.caching' => ".($_SESSION['cache']['feed.account_list.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['feed.account_list.caching'] ? "'cache.feed.account_list.backend.cache_dir' => '".$_SESSION['cache']['feed.account_list.backend.cache_dir']."'," : "")."

              'cache.twitter.accounts.caching' => ".($_SESSION['cache']['twitter.accounts.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['twitter.accounts.caching'] ? "'cache.twitter.accounts.backend.cache_dir' => '".$_SESSION['cache']['twitter.accounts.backend.cache_dir']."'," : "")."


              "
        : "'cache.default.caching' => 0,"
    ).
    "'application.htmlpurifier.preload_all' => " . ($_SESSION['application']['htmlpurifier.preload_all']  ? '1' : '0' ). ",
            'application.htmlpurifier.use_cache' => " . ($_SESSION['application']['htmlpurifier.use_cache'] ? '1' : '0'). ",
            'application.htmlpurifier.cache_dir' => " . ($_SESSION['application']['htmlpurifier.use_cache']
        ? "'" . $_SESSION['application']['htmlpurifier.cache_dir'] . "'"
        : "''") . ",

            'application.doctrine.cache.enabled' => " . ($_SESSION['application']['doctrine.cache.enabled']  ? '1' : '0' ). ",
            'application.doctrine.cache.query_cache.enabled' => " . ($_SESSION['application']['doctrine.cache.enabled']
    && $_SESSION['application']['doctrine.cache.query_cache.enabled']
        ? '1' : '0'). ",
            'application.doctrine.cache.query_cache.type' => '" . ($_SESSION['application']['doctrine.cache.enabled']
    && $_SESSION['application']['doctrine.cache.query_cache.enabled']
        ? $_SESSION['application']['doctrine.cache.query_cache.type']
        : ''). "',
            'application.doctrine.cache.query_cache.dir' => " . ($_SESSION['application']['doctrine.cache.enabled']
    && $_SESSION['application']['doctrine.cache.query_cache.enabled']
    && $_SESSION['application']['doctrine.cache.query_cache.type'] == 'file'
        ? "'" . $_SESSION['application']['doctrine.cache.query_cache.dir'] . "'"
        : "''") . ",

            'application.doctrine.cache.metadata_cache.enabled' => " . ($_SESSION['application']['doctrine.cache.enabled']
    && $_SESSION['application']['doctrine.cache.metadata_cache.enabled']
        ? '1'
        : '0'). ",
            'application.doctrine.cache.metadata_cache.type' => '" . ($_SESSION['application']['doctrine.cache.enabled']
    && $_SESSION['application']['doctrine.cache.metadata_cache.enabled']
        ? $_SESSION['application']['doctrine.cache.metadata_cache.type']
        : ''). "',
            'application.doctrine.cache.metadata_cache.dir' => " . ($_SESSION['application']['doctrine.cache.enabled']
    && $_SESSION['application']['doctrine.cache.metadata_cache.enabled']
    && $_SESSION['application']['doctrine.cache.metadata_cache.type'] == 'file'
        ? "'" . $_SESSION['application']['doctrine.cache.metadata_cache.dir'] . "'"
        : "''") . ",

            'files.upload.max_size' => " . $_SESSION['files']['upload.max_size']. ",
            'files.storage.filesystem.enabled' => " . ($_SESSION['files']['storage.filesystem.enabled'] ? '1' : '0'). ",
            'files.storage.filesystem.dir' => " . ($_SESSION['files']['storage.filesystem.enabled'] && $_SESSION['files']['storage.filesystem.dir']
        ? "'" . $_SESSION['files']['storage.filesystem.dir'] . "'"
        : "''") . ",

            'app_credentials'    => array('user' => '".$_SESSION['app_credentials']['user']."')
        );
    ";

    file_put_contents('../installation.info.php', $installationinfo);
    $installationinfo = "";

    echo "<script type=\"text/javascript\">this.location.href=\"./index.php?action=install_chunk_2\"</script>";